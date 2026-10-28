import React, { useState, useEffect, useRef, useMemo } from "react";
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ScrollView,
  ActivityIndicator,
  Alert,
  Modal,
  Dimensions,
  PermissionsAndroid,
  Platform,
  Image,
} from "react-native";
import Icon from "react-native-vector-icons/Feather";
import Svg, { Path, Defs, LinearGradient, Stop } from "react-native-svg";
import { WebView } from "react-native-webview";
import Geolocation from "@react-native-community/geolocation";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { Colors } from "../../settings/Colors";
import api from "../../api/axiosConfig";

const { width: SCREEN_WIDTH } = Dimensions.get("window");

export type DistanceUnit = "km" | "mil";
export const KM_TO_MILES = 0.62137119;
export const MILES_TO_KM = 1.609344;
const UNIT_STORAGE_KEY = "@akusehat_run_unit_preference";

// Leaflet Map with CartoDB / OSM tiles with Strava styling & Accuracy circle
const STRAVA_MAP_HTML = `
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body, #map { width: 100%; height: 100%; background: #0F172A; }
    .pulse-marker-strava {
      width: 20px;
      height: 20px;
      background: #FC5200;
      border: 3.5px solid #FFFFFF;
      border-radius: 50%;
      box-shadow: 0 0 12px rgba(252, 82, 0, 0.9);
      animation: stravaPulse 2s infinite;
    }
    @keyframes stravaPulse {
      0% { box-shadow: 0 0 0 0 rgba(252, 82, 0, 0.7); }
      70% { box-shadow: 0 0 0 14px rgba(252, 82, 0, 0); }
      100% { box-shadow: 0 0 0 0 rgba(252, 82, 0, 0); }
    }
    .start-marker {
      width: 16px;
      height: 16px;
      background: #10B981;
      border: 2.5px solid #FFFFFF;
      border-radius: 50%;
      box-shadow: 0 2px 6px rgba(0,0,0,0.4);
    }
    .leaflet-control-attribution { display: none !important; }
  </style>
</head>
<body>
  <div id="map"></div>
  <script>
    var map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-6.2088, 106.8456], 16);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
    }).addTo(map);

    var routePoints = [];
    var routePolyline = L.polyline([], {
      color: '#FC5200',
      weight: 5,
      opacity: 0.95,
      lineCap: 'round',
      lineJoin: 'round'
    }).addTo(map);

    var userMarker = null;
    var accuracyCircle = null;
    var startMarker = null;

    window.updateUserLocation = function(lat, lng, accuracy) {
      var latLng = [lat, lng];
      if (!userMarker) {
        var icon = L.divIcon({ className: 'pulse-marker-strava', iconSize: [20, 20], iconAnchor: [10, 10] });
        userMarker = L.marker(latLng, { icon: icon, zIndexOffset: 1000 }).addTo(map);
        map.setView(latLng, 16);
      } else {
        userMarker.setLatLng(latLng);
      }

      if (accuracy && accuracy > 0) {
        if (!accuracyCircle) {
          accuracyCircle = L.circle(latLng, {
            radius: accuracy,
            color: '#0284C7',
            fillColor: '#38BDF8',
            fillOpacity: 0.14,
            weight: 1.5
          }).addTo(map);
        } else {
          accuracyCircle.setLatLng(latLng);
          accuracyCircle.setRadius(accuracy);
        }
      }
    };

    window.addRoutePoint = function(lat, lng, accuracy) {
      var pt = [lat, lng];
      routePoints.push(pt);
      routePolyline.setLatLngs(routePoints);

      if (routePoints.length === 1 && !startMarker) {
        var startIcon = L.divIcon({ className: 'start-marker', iconSize: [16, 16], iconAnchor: [8, 8] });
        startMarker = L.marker(pt, { icon: startIcon }).addTo(map);
      }

      if (userMarker) {
        userMarker.setLatLng(pt);
      } else {
        var icon = L.divIcon({ className: 'pulse-marker-strava', iconSize: [20, 20], iconAnchor: [10, 10] });
        userMarker = L.marker(pt, { icon: icon, zIndexOffset: 1000 }).addTo(map);
      }

      if (accuracy && accuracy > 0) {
        if (!accuracyCircle) {
          accuracyCircle = L.circle(pt, {
            radius: accuracy,
            color: '#0284C7',
            fillColor: '#38BDF8',
            fillOpacity: 0.14,
            weight: 1.5
          }).addTo(map);
        } else {
          accuracyCircle.setLatLng(pt);
          accuracyCircle.setRadius(accuracy);
        }
      }

      map.panTo(pt, { animate: true, duration: 0.5 });
    };

    window.recenterMap = function() {
      if (userMarker) {
        map.setView(userMarker.getLatLng(), 17, { animate: true });
      } else if (routePoints.length > 0) {
        map.setView(routePoints[routePoints.length - 1], 17, { animate: true });
      }
    };

    window.clearRoute = function() {
      routePoints = [];
      routePolyline.setLatLngs([]);
      if (startMarker) { map.removeLayer(startMarker); startMarker = null; }
    };
  </script>
</body>
</html>
`;

function getHaversineDistanceKm(lat1: number, lon1: number, lat2: number, lon2: number): number {
  const R = 6371; // km
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLon = ((lon2 - lon1) * Math.PI) / 180;
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos((lat1 * Math.PI) / 180) *
    Math.cos((lat2 * Math.PI) / 180) *
    Math.sin(dLon / 2) *
    Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c;
}

function calculatePaceString(totalSeconds: number, km: number, targetUnit: DistanceUnit): string {
  const dist = targetUnit === "mil" ? km * KM_TO_MILES : km;
  const minThreshold = targetUnit === "mil" ? 0.03 : 0.05;
  if (dist < minThreshold || totalSeconds <= 0) {
    return "--:--";
  }
  const secPerUnit = totalSeconds / dist;
  if (!isFinite(secPerUnit) || secPerUnit > 3600) {
    return "--:--";
  }
  const mins = Math.floor(secPerUnit / 60);
  const secs = Math.floor(secPerUnit % 60);
  return `${mins < 10 ? "0" : ""}${mins}:${secs < 10 ? "0" : ""}${secs}`;
}

const requestLocationPermission = async () => {
  if (Platform.OS === "android") {
    try {
      const granted = await PermissionsAndroid.request(
        PermissionsAndroid.PERMISSIONS.ACCESS_FINE_LOCATION,
        {
          title: "Izin Lokasi GPS",
          message: "AkuSehat memerlukan akses GPS nyata agar rute dan jarak lari Anda terdeteksi akurat layaknya Strava.",
          buttonNeutral: "Nanti",
          buttonNegative: "Tolak",
          buttonPositive: "Izinkan",
        }
      );
      return granted === PermissionsAndroid.RESULTS.GRANTED;
    } catch (err) {
      console.warn("Location permission error:", err);
      return false;
    }
  }
  return true;
};

type RunActivity = {
  id: number;
  activity_type: string;
  title: string;
  distance_meters: number;
  duration_seconds: number;
  calories: number;
  avg_pace: string;
  steps: number;
  started_at: string;
};

type LeaderboardItem = {
  rank: number;
  user_id: string;
  name: string;
  avatar?: string | null;
  divisi?: string;
  instansi?: string;
  is_current_user?: boolean;
  total_distance_km: number;
  total_duration_seconds: number;
  formatted_duration?: string;
  best_pace_seconds?: number;
  avg_pace_seconds?: number;
  total_calories?: number;
  total_sessions?: number;
};

const LaurelWreathLeft = ({ size = 110 }: { size?: number }) => (
  <Svg width={size * 0.42} height={size} viewBox="0 0 50 120" fill="none">
    <Defs>
      <LinearGradient id="laurelGrad" x1="0%" y1="0%" x2="100%" y2="100%">
        <Stop offset="0%" stopColor="#FDE68A" />
        <Stop offset="45%" stopColor="#F59E0B" />
        <Stop offset="100%" stopColor="#B45309" />
      </LinearGradient>
    </Defs>
    <Path
      d="M42 110 C25 85 18 50 34 12"
      stroke="url(#laurelGrad)"
      strokeWidth="2.2"
      strokeLinecap="round"
    />
    <Path d="M40 102 C32 100 28 106 31 110 C35 114 41 108 40 102Z" fill="url(#laurelGrad)" />
    <Path d="M36 88 C26 86 23 93 27 97 C31 101 38 94 36 88Z" fill="url(#laurelGrad)" />
    <Path d="M31 74 C19 72 17 80 21 84 C26 88 33 80 31 74Z" fill="url(#laurelGrad)" />
    <Path d="M28 58 C15 56 14 65 19 69 C25 73 31 64 28 58Z" fill="url(#laurelGrad)" />
    <Path d="M26 42 C14 38 15 48 20 52 C26 56 29 47 26 42Z" fill="url(#laurelGrad)" />
    <Path d="M28 27 C18 22 21 32 26 36 C30 40 32 32 28 27Z" fill="url(#laurelGrad)" />
    <Path d="M34 12 C28 6 34 9 37 14 C40 18 38 19 34 12Z" fill="url(#laurelGrad)" />
  </Svg>
);

const LaurelWreathRight = ({ size = 110 }: { size?: number }) => (
  <View style={{ transform: [{ scaleX: -1 }] }}>
    <LaurelWreathLeft size={size} />
  </View>
);

const formatLeaderboardPace = (paceSecPerKm: number | undefined, currentUnit: DistanceUnit) => {
  if (!paceSecPerKm || paceSecPerKm <= 0) return `--:--/${currentUnit}`;
  const sec = currentUnit === "mil" ? Math.round(paceSecPerKm / KM_TO_MILES) : Math.round(paceSecPerKm);
  const m = Math.floor(sec / 60);
  const s = Math.floor(sec % 60);
  return `${m}:${s < 10 ? "0" : ""}${s}/${currentUnit}`;
};

const formatLeaderboardDistance = (distKm: number | undefined, currentUnit: DistanceUnit) => {
  if (!distKm || distKm <= 0) return `0.0 ${currentUnit.toUpperCase()}`;
  const val = currentUnit === "mil" ? (distKm * KM_TO_MILES).toFixed(1) : distKm.toFixed(1);
  return `${val} ${currentUnit.toUpperCase()}`;
};

const formatLeaderboardDuration = (totalSec: number | undefined) => {
  if (!totalSec || totalSec <= 0) return "0m";
  const hours = Math.floor(totalSec / 3600);
  const minutes = Math.floor((totalSec % 3600) / 60);
  if (hours > 0) {
    return `${hours}j ${minutes}m`;
  }
  return `${minutes}m`;
};

export default function RunTrackerView() {
  // Tabs: 'tracker' | 'history' | 'leaderboard'
  const [subTab, setSubTab] = useState<"tracker" | "history" | "leaderboard">("tracker");

  // Unit Preference ('km' | 'mil')
  const [unit, setUnit] = useState<DistanceUnit>("km");

  // Running State
  const [isRunning, setIsRunning] = useState(false);
  const [isPaused, setIsPaused] = useState(false);
  const [seconds, setSeconds] = useState(0);
  const [distanceKm, setDistanceKm] = useState(0);
  const [calories, setCalories] = useState(0);
  const [steps, setSteps] = useState(0);
  const [activityType, setActivityType] = useState<"run" | "walk" | "cycling">("run");

  // Movement & Anti-Drift State
  const [movementStatus, setMovementStatus] = useState<"moving" | "stationary" | "idle">("idle");
  const [currentSpeedKmh, setCurrentSpeedKmh] = useState<number>(0);

  // GPS & Map State
  const [gpsStatus, setGpsStatus] = useState<"searching" | "connected" | "denied">("searching");
  const [currentCoords, setCurrentCoords] = useState<{ latitude: number; longitude: number } | null>(null);
  const [isSimulationMode, setIsSimulationMode] = useState(false);
  const webViewRef = useRef<any>(null);
  const lastCoordRef = useRef<{ latitude: number; longitude: number } | null>(null);
  const lastTimestampRef = useRef<number | null>(null);
  const watchIdRef = useRef<number | null>(null);

  // Finish Modal
  const [showFinishModal, setShowFinishModal] = useState(false);
  const [isSaving, setIsSaving] = useState(false);

  // History & Leaderboard Data
  const [activities, setActivities] = useState<RunActivity[]>([]);
  const [loadingHistory, setLoadingHistory] = useState(false);
  const [summaryStats, setSummaryStats] = useState({
    total_km: 0,
    total_runs: 0,
    total_calories: 0,
  });

  const [leaderboardData, setLeaderboardData] = useState<LeaderboardItem[]>([]);
  const [loadingLeaderboard, setLoadingLeaderboard] = useState(false);
  const [leaderboardPeriod, setLeaderboardPeriod] = useState<"month" | "year" | "last_year">("month");
  const [leaderboardScope, setLeaderboardScope] = useState<"local" | "global">("local");
  const [entityType, setEntityType] = useState<"Institusi" | "Instansi">("Instansi");
  const [institutionName, setInstitutionName] = useState<string>("");

  const timerRef = useRef<any>(null);
  const startTimeRef = useRef<Date | null>(null);

  // Load Unit Preference & User Entity Type from AsyncStorage
  useEffect(() => {
    AsyncStorage.getItem(UNIT_STORAGE_KEY)
      .then((saved) => {
        if (saved === "km" || saved === "mil") {
          setUnit(saved);
        }
      })
      .catch(() => {});

    AsyncStorage.getItem("user")
      .then((userStr) => {
        if (userStr) {
          try {
            const u = JSON.parse(userStr);
            const ins = u?.instansi || u?.sekolah;
            if (ins?.nama_instansi) {
              setInstitutionName(ins.nama_instansi);
            }
            if (ins?.tipe_sebutan) {
              setEntityType(ins.tipe_sebutan === "Institusi" ? "Institusi" : "Instansi");
            } else if (ins?.jenis_instansi) {
              const isEdu = ins.jenis_instansi === "Sekolah" || ins.jenis_instansi === "Universitas" || !!ins.npsn;
              setEntityType(isEdu ? "Institusi" : "Instansi");
            }
          } catch {}
        }
      })
      .catch(() => {});
  }, []);

  const handleToggleUnit = (newUnit: DistanceUnit) => {
    setUnit(newUnit);
    AsyncStorage.setItem(UNIT_STORAGE_KEY, newUnit).catch(() => {});
  };

  // Derived Values for Display
  const displayDistance = useMemo(() => {
    if (unit === "mil") {
      return (distanceKm * KM_TO_MILES).toFixed(2);
    }
    return distanceKm.toFixed(2);
  }, [distanceKm, unit]);

  const currentPace = useMemo(() => {
    return calculatePaceString(seconds, distanceKm, unit);
  }, [seconds, distanceKm, unit]);

  const displaySpeed = useMemo(() => {
    if (unit === "mil") {
      return `${(currentSpeedKmh * KM_TO_MILES).toFixed(1)} mph`;
    }
    return `${currentSpeedKmh.toFixed(1)} km/j`;
  }, [currentSpeedKmh, unit]);

  // Inisialisasi GPS & Lokasi Awal
  useEffect(() => {
    let isMounted = true;
    (async () => {
      const ok = await requestLocationPermission();
      if (!ok) {
        if (isMounted) setGpsStatus("denied");
        return;
      }
      Geolocation.getCurrentPosition(
        (pos) => {
          if (!isMounted) return;
          const { latitude, longitude, accuracy } = pos.coords;
          setCurrentCoords({ latitude, longitude });
          setGpsStatus("connected");
          webViewRef.current?.injectJavaScript(
            `if (window.updateUserLocation) { window.updateUserLocation(${latitude}, ${longitude}, ${accuracy || 10}); } true;`
          );
        },
        (err) => {
          console.warn("GPS init error", err);
          if (isMounted) setGpsStatus("connected");
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 10000 }
      );
    })();

    return () => {
      isMounted = false;
      if (watchIdRef.current !== null) {
        Geolocation.clearWatch(watchIdRef.current);
      }
    };
  }, []);

  // GPS Live Tracking dengan Filter Akurasi, Anti-Drift saat Diam, dan Anti-Teleport
  useEffect(() => {
    if (isRunning && !isPaused && !isSimulationMode) {
      watchIdRef.current = Geolocation.watchPosition(
        (pos) => {
          const { latitude, longitude, accuracy, speed } = pos.coords;
          const timestamp = pos.timestamp || Date.now();
          setCurrentCoords({ latitude, longitude });
          setGpsStatus("connected");

          // 1. FILTER AKURASI BURUK (> 25 meter):
          // Jika GPS belum akurat, jangan tambahkan jarak atau rute, hanya perbarui posisi di peta
          if (accuracy && accuracy > 25) {
            webViewRef.current?.injectJavaScript(
              `if (window.updateUserLocation) { window.updateUserLocation(${latitude}, ${longitude}, ${accuracy}); } true;`
            );
            return;
          }

          if (!lastCoordRef.current) {
            lastCoordRef.current = { latitude, longitude };
            lastTimestampRef.current = timestamp;
            setMovementStatus("idle");
            webViewRef.current?.injectJavaScript(
              `if (window.addRoutePoint) { window.addRoutePoint(${latitude}, ${longitude}, ${accuracy || 10}); } true;`
            );
            return;
          }

          const distDeltaKm = getHaversineDistanceKm(
            lastCoordRef.current.latitude,
            lastCoordRef.current.longitude,
            latitude,
            longitude
          );
          const distDeltaMeters = distDeltaKm * 1000;
          const timeDeltaSec = lastTimestampRef.current
            ? Math.max(0.5, (timestamp - lastTimestampRef.current) / 1000)
            : 1;
          const computedSpeedMs = distDeltaMeters / timeDeltaSec;

          const hasHardwareSpeed = speed !== null && speed !== undefined && speed >= 0;
          const effectiveSpeedMs = hasHardwareSpeed ? speed : computedSpeedMs;
          const effectiveSpeedKmh = effectiveSpeedMs * 3.6;

          // 2. FILTER ANOMALI LONCATAN (ANTI-TELEPORT / GPS GLITCH):
          const maxAllowedKmh = activityType === "cycling" ? 65 : 35;
          if (effectiveSpeedKmh > maxAllowedKmh) {
            // Loncatan sinyal ekstrem, tolak koordinat ini
            return;
          }

          // 3. DETEKSI STATUS GERAK VS DIAM (ANTI-DRIFT AKURAT):
          // Ketika ponsel diam / user berhenti:
          // Doppler hardware speed < 0.6 m/s (2.16 km/j) ATAU
          // Perpindahan jarak berada dalam batas noise satelit: Math.max(4.0, (accuracy || 8) * 0.55)
          const dynamicNoiseMargin = Math.max(4.0, (accuracy || 8) * 0.55);
          const isStationary =
            (hasHardwareSpeed && speed < 0.6) || distDeltaMeters < dynamicNoiseMargin;

          if (isStationary) {
            // PERANGKAT DIAM / TIDAK BERGERAK -> JARAK TIDAK BERTAMBAH!
            setMovementStatus("stationary");
            setCurrentSpeedKmh(0);
            webViewRef.current?.injectJavaScript(
              `if (window.updateUserLocation) { window.updateUserLocation(${latitude}, ${longitude}, ${accuracy || 10}); } true;`
            );
          } else {
            // PERANGKAT BENAR-BENAR BERGERAK -> AKUMULASIKAN JARAK & RUTE PETA
            setMovementStatus("moving");
            setCurrentSpeedKmh(effectiveSpeedKmh);
            lastCoordRef.current = { latitude, longitude };
            lastTimestampRef.current = timestamp;

            setDistanceKm((prevKm) => {
              const nextDistKm = prevKm + distDeltaKm;
              const calRate = activityType === "run" ? 62 : activityType === "cycling" ? 35 : 45;
              setCalories(Math.round(nextDistKm * calRate * 10) / 10);
              setSteps(Math.round(nextDistKm * 1350));
              return nextDistKm;
            });

            webViewRef.current?.injectJavaScript(
              `if (window.addRoutePoint) { window.addRoutePoint(${latitude}, ${longitude}, ${accuracy || 10}); } true;`
            );
          }
        },
        (err) => console.warn("GPS tracking error:", err),
        { enableHighAccuracy: true, distanceFilter: 1, interval: 1000, fastestInterval: 1000 }
      );
    } else {
      if (watchIdRef.current !== null) {
        Geolocation.clearWatch(watchIdRef.current);
        watchIdRef.current = null;
      }
    }

    return () => {
      if (watchIdRef.current !== null) {
        Geolocation.clearWatch(watchIdRef.current);
        watchIdRef.current = null;
      }
    };
  }, [isRunning, isPaused, isSimulationMode, activityType]);

  // Timer Tick (Penghitung detik durasi workout)
  useEffect(() => {
    if (isRunning && !isPaused) {
      timerRef.current = setInterval(() => {
        setSeconds((prev) => {
          const nextSec = prev + 1;

          // JIKA MODE SIMULASI DIAKTIFKAN (untuk pengujian):
          if (isSimulationMode) {
            const speedKmH = activityType === "run" ? 8.5 : activityType === "cycling" ? 18.0 : 4.8;
            const addedDistanceKm = speedKmH / 3600;

            setMovementStatus("moving");
            setCurrentSpeedKmh(speedKmH);

            setDistanceKm((prevDist) => {
              const nextDist = prevDist + addedDistanceKm;
              const calRate = activityType === "run" ? 62 : activityType === "cycling" ? 35 : 45;
              setCalories(Math.round(nextDist * calRate * 10) / 10);
              setSteps(Math.round(nextDist * 1350));
              return nextDist;
            });

            // Simulasi titik rute GPS dinamis pada peta
            const baseLat = currentCoords?.latitude || -6.2088;
            const baseLng = currentCoords?.longitude || 106.8456;
            const angle = (nextSec * 6 * Math.PI) / 180;
            const radius = 0.0015;
            const simLat = baseLat + Math.sin(angle) * radius;
            const simLng = baseLng + Math.cos(angle) * radius;

            webViewRef.current?.injectJavaScript(
              `if (window.addRoutePoint) { window.addRoutePoint(${simLat}, ${simLng}, 5); } true;`
            );
          }

          return nextSec;
        });
      }, 1000);
    } else {
      if (timerRef.current) clearInterval(timerRef.current);
    }

    return () => {
      if (timerRef.current) clearInterval(timerRef.current);
    };
  }, [isRunning, isPaused, isSimulationMode, activityType, currentCoords]);

  // Load history when tab changes
  useEffect(() => {
    if (subTab === "history") {
      fetchHistory();
    } else if (subTab === "leaderboard") {
      fetchLeaderboard(leaderboardPeriod, leaderboardScope);
    }
  }, [subTab, leaderboardPeriod, leaderboardScope]);

  const fetchHistory = async () => {
    setLoadingHistory(true);
    try {
      const res = await api.get("/activities");
      if (res.data?.success) {
        setActivities(res.data.data.data || []);
        setSummaryStats({
          total_km: res.data.summary.total_distance_km || 0,
          total_runs: res.data.summary.total_activities || 0,
          total_calories: res.data.summary.total_calories || 0,
        });
      }
    } catch (e) {
      console.log("Error fetch activities", e);
    } finally {
      setLoadingHistory(false);
    }
  };

  const fetchLeaderboard = async (targetPeriod = leaderboardPeriod, targetScope = leaderboardScope) => {
    setLoadingLeaderboard(true);
    try {
      // High Performance Pooling: Concurrently fetch Top 3 Podium and Top 4-10 Runners over 1 connection pool
      const [podiumRes, runnersRes] = await Promise.all([
        api.get(`/activities/leaderboard/podium?period=${targetPeriod}&scope=${targetScope}`),
        api.get(`/activities/leaderboard/runners?period=${targetPeriod}&scope=${targetScope}`),
      ]);

      const podium = podiumRes.data?.success ? (podiumRes.data.data || []) : [];
      const runners = runnersRes.data?.success ? (runnersRes.data.data || []) : [];

      const respEntityType = podiumRes.data?.tipe_sebutan || runnersRes.data?.tipe_sebutan;
      if (respEntityType) {
        setEntityType(respEntityType === "Institusi" ? "Institusi" : "Instansi");
      }
      const respInstName = podiumRes.data?.instansi_name || runnersRes.data?.instansi_name;
      if (respInstName) {
        setInstitutionName(respInstName);
      }

      setLeaderboardData([...podium, ...runners]);
    } catch (e) {
      console.log("Error pooled fetch leaderboard, falling back to unified endpoint:", e);
      try {
        const fallbackRes = await api.get(`/activities/leaderboard?period=${targetPeriod}&scope=${targetScope}`);
        if (fallbackRes.data?.success) {
          if (fallbackRes.data?.tipe_sebutan) {
            setEntityType(fallbackRes.data.tipe_sebutan === "Institusi" ? "Institusi" : "Instansi");
          }
          if (fallbackRes.data?.instansi_name) {
            setInstitutionName(fallbackRes.data.instansi_name);
          }
          setLeaderboardData(fallbackRes.data.data || []);
        }
      } catch (fallbackErr) {
        console.log("Error fallback fetch leaderboard:", fallbackErr);
      }
    } finally {
      setLoadingLeaderboard(false);
    }
  };

  const startRun = () => {
    setIsRunning(true);
    setIsPaused(false);
    setSeconds(0);
    setDistanceKm(0);
    setCalories(0);
    setSteps(0);
    setCurrentSpeedKmh(0);
    setMovementStatus("idle");
    lastCoordRef.current = null;
    lastTimestampRef.current = null;
    startTimeRef.current = new Date();
    webViewRef.current?.injectJavaScript(
      "if (window.clearRoute) { window.clearRoute(); } if (window.recenterMap) { window.recenterMap(); } true;"
    );
  };

  const pauseRun = () => {
    setIsPaused(true);
    setMovementStatus("stationary");
    setCurrentSpeedKmh(0);
  };

  const resumeRun = () => {
    setIsPaused(false);
  };

  const finishRun = () => {
    setIsPaused(true);
    setMovementStatus("stationary");
    if (seconds < 10) {
      Alert.alert(
        "Aktivitas Terlalu Singkat",
        "Durasi aktivitas kurang dari 10 detik dan tidak akan disimpan.",
        [
          {
            text: "Oke",
            onPress: () => {
              setIsRunning(false);
              setIsPaused(false);
              setSeconds(0);
            },
          },
        ]
      );
      return;
    }
    setShowFinishModal(true);
  };

  const handleSaveActivity = async () => {
    setIsSaving(true);
    try {
      const payload = {
        activity_type: activityType,
        distance_meters: Math.round(distanceKm * 1000),
        duration_seconds: seconds,
        calories: calories,
        avg_pace: `${currentPace}/${unit}`,
        steps: steps,
        started_at: startTimeRef.current?.toISOString() || new Date().toISOString(),
        ended_at: new Date().toISOString(),
        finished_at: new Date().toISOString(),
      };

      const res = await api.post("/activities", payload);
      if (res.data?.success) {
        Alert.alert("Luar Biasa! 🎉", "Aktivitas olahraga Anda berhasil dicatat!");
        setShowFinishModal(false);
        setIsRunning(false);
        setIsPaused(false);
        setSeconds(0);
        setDistanceKm(0);
        setSubTab("history");
      }
    } catch (e: any) {
      const msg = e?.response?.data?.message || "Terjadi kesalahan saat menyimpan data aktivitas.";
      Alert.alert("Gagal Menyimpan", msg);
    } finally {
      setIsSaving(false);
    }
  };

  const formatTimer = (totalSec: number) => {
    const hrs = Math.floor(totalSec / 3600);
    const mins = Math.floor((totalSec % 3600) / 60);
    const secs = totalSec % 60;
    if (hrs > 0) {
      return `${hrs}:${mins < 10 ? "0" : ""}${mins}:${secs < 10 ? "0" : ""}${secs}`;
    }
    return `${mins < 10 ? "0" : ""}${mins}:${secs < 10 ? "0" : ""}${secs}`;
  };

  return (
    <View style={styles.container}>
      {/* Sub-Tab Navigation Header */}
      <View style={styles.tabHeader}>
        <TouchableOpacity
          style={[styles.tabButton, subTab === "tracker" && styles.tabButtonActive]}
          onPress={() => setSubTab("tracker")}
        >
          <Icon
            name="play-circle"
            size={16}
            color={subTab === "tracker" ? Colors.primary : Colors.muted}
          />
          <Text
            style={[styles.tabButtonText, subTab === "tracker" && styles.tabButtonTextActive]}
          >
            Lari / Track
          </Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.tabButton, subTab === "history" && styles.tabButtonActive]}
          onPress={() => setSubTab("history")}
        >
          <Icon
            name="calendar"
            size={16}
            color={subTab === "history" ? Colors.primary : Colors.muted}
          />
          <Text
            style={[styles.tabButtonText, subTab === "history" && styles.tabButtonTextActive]}
          >
            Riwayat
          </Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.tabButton, subTab === "leaderboard" && styles.tabButtonActive]}
          onPress={() => setSubTab("leaderboard")}
        >
          <Icon
            name="award"
            size={16}
            color={subTab === "leaderboard" ? Colors.primary : Colors.muted}
          />
          <Text
            style={[
              styles.tabButtonText,
              subTab === "leaderboard" && styles.tabButtonTextActive,
            ]}
          >
            Peringkat
          </Text>
        </TouchableOpacity>
      </View>

      {/* TAB 1: RUN TRACKER */}
      {subTab === "tracker" && (
        <View style={styles.scrollContent}>
          {/* Mode Selector */}
          {!isRunning && (
            <View style={styles.modeContainer}>
              <TouchableOpacity
                style={[styles.modePill, activityType === "run" && styles.modePillActive]}
                onPress={() => setActivityType("run")}
              >
                <Icon
                  name="zap"
                  size={16}
                  color={activityType === "run" ? "#fff" : Colors.muted}
                />
                <Text
                  style={[
                    styles.modePillText,
                    activityType === "run" && styles.modePillTextActive,
                  ]}
                >
                  Lari
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[styles.modePill, activityType === "walk" && styles.modePillActive]}
                onPress={() => setActivityType("walk")}
              >
                <Icon
                  name="user"
                  size={16}
                  color={activityType === "walk" ? "#fff" : Colors.muted}
                />
                <Text
                  style={[
                    styles.modePillText,
                    activityType === "walk" && styles.modePillTextActive,
                  ]}
                >
                  Jalan
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[styles.modePill, activityType === "cycling" && styles.modePillActive]}
                onPress={() => setActivityType("cycling")}
              >
                <Icon
                  name="compass"
                  size={16}
                  color={activityType === "cycling" ? "#fff" : Colors.muted}
                />
                <Text
                  style={[
                    styles.modePillText,
                    activityType === "cycling" && styles.modePillTextActive,
                  ]}
                >
                  Sepeda
                </Text>
              </TouchableOpacity>
            </View>
          )}

          {/* Strava Live Map Card */}
          <View style={styles.mapCard}>
            <WebView
              ref={webViewRef}
              originWhitelist={["*"]}
              source={{ html: STRAVA_MAP_HTML }}
              style={styles.mapWebView}
              javaScriptEnabled={true}
              domStorageEnabled={true}
              scrollEnabled={false}
              onLoadEnd={() => {
                if (currentCoords) {
                  webViewRef.current?.injectJavaScript(
                    `if (window.updateUserLocation) { window.updateUserLocation(${currentCoords.latitude}, ${currentCoords.longitude}, 10); } true;`
                  );
                }
              }}
            />

            {/* Floating Top Bar: GPS Status & Simulation Toggle */}
            <View style={styles.mapTopFloatingRow} pointerEvents="box-none">
              <View
                style={[
                  styles.gpsBadge,
                  gpsStatus === "connected" ? styles.gpsBadgeOk : styles.gpsBadgeSearching,
                ]}
              >
                <View
                  style={[
                    styles.gpsDot,
                    gpsStatus === "connected" ? styles.gpsDotOk : styles.gpsDotSearching,
                  ]}
                />
                <Text style={styles.gpsBadgeText}>
                  {gpsStatus === "connected"
                    ? isSimulationMode
                      ? "Demo Simulasi"
                      : "GPS Nyata Aktif"
                    : gpsStatus === "denied"
                      ? "Izin GPS Ditolak"
                      : "Mencari GPS..."}
                </Text>
              </View>

              {/* Movement / Stationary Indicator */}
              {isRunning && !isPaused && (
                <View
                  style={[
                    styles.movementBadge,
                    movementStatus === "moving"
                      ? styles.movementBadgeMoving
                      : styles.movementBadgeStationary,
                  ]}
                >
                  <View
                    style={[
                      styles.movementDot,
                      movementStatus === "moving"
                        ? styles.movementDotMoving
                        : styles.movementDotStationary,
                    ]}
                  />
                  <Text style={styles.movementBadgeText}>
                    {movementStatus === "moving"
                      ? `Bergerak • ${displaySpeed}`
                      : "Diam (Anti-Drift)"}
                  </Text>
                </View>
              )}

              <TouchableOpacity
                style={styles.mapModeBadge}
                onPress={() => setIsSimulationMode(!isSimulationMode)}
                hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
                activeOpacity={0.7}
              >
                <Icon
                  name={isSimulationMode ? "cpu" : "navigation"}
                  size={12}
                  color={isSimulationMode ? "#D97706" : Colors.primary}
                />
                <Text
                  style={[
                    styles.mapModeBadgeText,
                    { color: isSimulationMode ? "#D97706" : Colors.primary },
                  ]}
                >
                  {isSimulationMode ? "Mode Demo" : "GPS Nyata"}
                </Text>
              </TouchableOpacity>
            </View>

            {/* Floating Recenter Button */}
            <TouchableOpacity
              style={styles.mapRecenterBtn}
              onPress={() => {
                webViewRef.current?.injectJavaScript(
                  "if (window.recenterMap) { window.recenterMap(); } true;"
                );
              }}
            >
              <Icon name="crosshair" size={18} color="#0F172A" />
            </TouchableOpacity>
          </View>

          {/* Main Digital Dashboard */}
          <View style={styles.dashboardCard}>
            {/* Top Bar inside Dashboard: Status & Unit Toggle */}
            <View style={styles.dashboardHeaderRow}>
              <View style={styles.statusIndicatorBox}>
                <View
                  style={[
                    styles.statusPillDot,
                    isRunning && !isPaused && { backgroundColor: "#10B981" },
                    isRunning && isPaused && { backgroundColor: "#F59E0B" },
                  ]}
                />
                <Text style={styles.dashboardLabel}>
                  {isRunning
                    ? isPaused
                      ? "WORKOUT DIJEDA"
                      : movementStatus === "moving"
                        ? "SEDANG BERGERAK"
                        : "SEDANG BERLARI (DIAM)"
                    : "SIAP MEMULAI OLAHRAGA"}
                </Text>
              </View>

              {/* Unit Selector Toggle (KM vs MIL) */}
              <View style={styles.unitToggleGroup}>
                <TouchableOpacity
                  style={[
                    styles.unitToggleBtn,
                    unit === "km" && styles.unitToggleBtnActive,
                  ]}
                  onPress={() => handleToggleUnit("km")}
                  activeOpacity={0.8}
                >
                  <Text
                    style={[
                      styles.unitToggleText,
                      unit === "km" && styles.unitToggleTextActive,
                    ]}
                  >
                    KM
                  </Text>
                </TouchableOpacity>

                <TouchableOpacity
                  style={[
                    styles.unitToggleBtn,
                    unit === "mil" && styles.unitToggleBtnActive,
                  ]}
                  onPress={() => handleToggleUnit("mil")}
                  activeOpacity={0.8}
                >
                  <Text
                    style={[
                      styles.unitToggleText,
                      unit === "mil" && styles.unitToggleTextActive,
                    ]}
                  >
                    MIL
                  </Text>
                </TouchableOpacity>
              </View>
            </View>

            {/* Big Distance */}
            <View style={styles.distanceRow}>
              <Text style={styles.bigDistanceNumber}>{displayDistance}</Text>
              <View style={styles.distanceUnitBadge}>
                <Text style={styles.distanceUnit}>{unit.toUpperCase()}</Text>
              </View>
            </View>

            {/* Metrics 3-Col Grid */}
            <View style={styles.metricsGrid}>
              <View style={styles.metricItem}>
                <Text style={styles.metricLabel}>WAKTU</Text>
                <Text style={styles.metricValue}>{formatTimer(seconds)}</Text>
              </View>

              <View style={styles.metricItem}>
                <Text style={styles.metricLabel}>PACE (/{unit.toUpperCase()})</Text>
                <Text style={styles.metricValue}>{currentPace}</Text>
              </View>

              <View style={styles.metricItem}>
                <Text style={styles.metricLabel}>KALORI</Text>
                <Text style={styles.metricValue}>{Math.round(calories)} kcal</Text>
              </View>
            </View>

            {/* Steps & Speed Banner */}
            <View style={styles.stepsBadge}>
              <Icon name="activity" size={14} color={Colors.primary} />
              <Text style={styles.stepsBadgeText}>
                {steps} Langkah &bull; {isRunning ? displaySpeed : "Target: 7.000 Langkah"}
              </Text>
            </View>
          </View>

          {/* Action Control Buttons */}
          <View style={styles.actionContainer}>
            {!isRunning ? (
              <TouchableOpacity
                style={styles.startBigButton}
                activeOpacity={0.85}
                onPress={startRun}
              >
                <Icon name="play" size={28} color="#fff" />
                <Text style={styles.startBigButtonText}>MULAI AKTIVITAS</Text>
              </TouchableOpacity>
            ) : (
              <View style={styles.controlRow}>
                {isPaused ? (
                  <TouchableOpacity
                    style={[styles.circleButton, { backgroundColor: Colors.primary }]}
                    onPress={resumeRun}
                  >
                    <Icon name="play" size={26} color="#fff" />
                    <Text style={styles.circleBtnText}>Lanjut</Text>
                  </TouchableOpacity>
                ) : (
                  <TouchableOpacity
                    style={[styles.circleButton, { backgroundColor: Colors.warning }]}
                    onPress={pauseRun}
                  >
                    <Icon name="pause" size={26} color="#fff" />
                    <Text style={styles.circleBtnText}>Jeda</Text>
                  </TouchableOpacity>
                )}

                <TouchableOpacity
                  style={[styles.circleButton, { backgroundColor: Colors.error }]}
                  onPress={finishRun}
                >
                  <Icon name="square" size={26} color="#fff" />
                  <Text style={styles.circleBtnText}>Selesai</Text>
                </TouchableOpacity>
              </View>
            )}
          </View>

          {/* Quick Motivational Tips */}
          <View style={styles.tipsCard}>
            <View style={styles.tipsIconBox}>
              <Icon name="info" size={20} color={Colors.primary} />
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.tipsTitle}>Tips Kebugaran & Akurasi</Text>
              <Text style={styles.tipsBody}>
                Fitur lari dilengkapi sensor anti-drift cerdas. Jika Anda berhenti di lampu merah atau mengikat tali sepatu, jarak tidak akan bertambah sembarangan.
              </Text>
            </View>
          </View>
        </View>
      )}

      {/* TAB 2: RIWAYAT AKTIVITAS */}
      {subTab === "history" && (
        <View style={styles.scrollContent}>
          {/* Summary Banner with Unit Switcher */}
          <View style={styles.historyStatsCard}>
            <View style={styles.historyHeaderRow}>
              <Text style={styles.historySummaryTitle}>Ringkasan Statistik</Text>
              <View style={styles.unitToggleGroupLight}>
                <TouchableOpacity
                  style={[
                    styles.unitToggleBtnLight,
                    unit === "km" && styles.unitToggleBtnActiveLight,
                  ]}
                  onPress={() => handleToggleUnit("km")}
                >
                  <Text
                    style={[
                      styles.unitToggleTextLight,
                      unit === "km" && styles.unitToggleTextActiveLight,
                    ]}
                  >
                    KM
                  </Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={[
                    styles.unitToggleBtnLight,
                    unit === "mil" && styles.unitToggleBtnActiveLight,
                  ]}
                  onPress={() => handleToggleUnit("mil")}
                >
                  <Text
                    style={[
                      styles.unitToggleTextLight,
                      unit === "mil" && styles.unitToggleTextActiveLight,
                    ]}
                  >
                    MIL
                  </Text>
                </TouchableOpacity>
              </View>
            </View>

            <View style={styles.statBoxesRow}>
              <View style={styles.statBox}>
                <Text style={styles.statNumber}>
                  {unit === "mil"
                    ? (summaryStats.total_km * KM_TO_MILES).toFixed(1)
                    : summaryStats.total_km.toFixed(1)}
                </Text>
                <Text style={styles.statLabel}>Total {unit.toUpperCase()}</Text>
              </View>
              <View style={styles.statDivider} />
              <View style={styles.statBox}>
                <Text style={styles.statNumber}>{summaryStats.total_runs}</Text>
                <Text style={styles.statLabel}>Total Sesi</Text>
              </View>
              <View style={styles.statDivider} />
              <View style={styles.statBox}>
                <Text style={styles.statNumber}>{Math.round(summaryStats.total_calories)}</Text>
                <Text style={styles.statLabel}>Kkal Terbakar</Text>
              </View>
            </View>
          </View>

          <Text style={styles.sectionHeaderTitle}>Aktivitas Terakhir</Text>

          {loadingHistory ? (
            <ActivityIndicator size="large" color={Colors.primary} style={{ marginTop: 24 }} />
          ) : activities.length === 0 ? (
            <View style={styles.emptyContainer}>
              <Icon name="compass" size={48} color={Colors.muted} />
              <Text style={styles.emptyTitle}>Belum Ada Aktivitas Olahraga</Text>
              <Text style={styles.emptySubtitle}>
                Ayo mulai sesi lari atau jalan santai pertamamu hari ini!
              </Text>
            </View>
          ) : (
            activities.map((act) => {
              const actKm = (act.distance_meters || 0) / 1000;
              const formattedDist =
                unit === "mil"
                  ? `${(actKm * KM_TO_MILES).toFixed(2)} MIL`
                  : `${actKm.toFixed(2)} KM`;

              return (
                <View key={act.id} style={styles.activityItemCard}>
                  <View style={styles.activityHeader}>
                    <View style={styles.activityIconCircle}>
                      <Icon
                        name={act.activity_type === "run" ? "zap" : act.activity_type === "cycling" ? "compass" : "user"}
                        size={18}
                        color={Colors.primary}
                      />
                    </View>
                    <View style={{ flex: 1, marginLeft: 12 }}>
                      <Text style={styles.activityTitle}>{act.title}</Text>
                      <Text style={styles.activityDate}>
                        {new Date(act.started_at).toLocaleDateString("id-ID", {
                          weekday: "short",
                          day: "numeric",
                          month: "short",
                          year: "numeric",
                        })}
                      </Text>
                    </View>
                    <Text style={styles.activityKmBadge}>{formattedDist}</Text>
                  </View>

                  <View style={styles.activityDetailRow}>
                    <View style={styles.activityDetailCol}>
                      <Text style={styles.detailLabel}>Durasi</Text>
                      <Text style={styles.detailValue}>
                        {Math.floor(act.duration_seconds / 60)}m {act.duration_seconds % 60}s
                      </Text>
                    </View>
                    <View style={styles.activityDetailCol}>
                      <Text style={styles.detailLabel}>Pace</Text>
                      <Text style={styles.detailValue}>
                        {act.avg_pace || "-"}
                      </Text>
                    </View>
                    <View style={styles.activityDetailCol}>
                      <Text style={styles.detailLabel}>Kalori</Text>
                      <Text style={styles.detailValue}>{Math.round(act.calories)} kcal</Text>
                    </View>
                  </View>
                </View>
              );
            })
          )}
        </View>
      )}

      {/* TAB 3: LEADERBOARD PERINGKAT */}
      {subTab === "leaderboard" && (
        <View style={styles.scrollContent}>
          {/* Scope Selector: Lokal (Instansi) vs Global */}
          <View style={styles.scopeFilterRow}>
            <TouchableOpacity
              style={[
                styles.scopeBtn,
                leaderboardScope === "local" && styles.scopeBtnActive,
              ]}
              onPress={() => setLeaderboardScope("local")}
              activeOpacity={0.8}
            >
              <Icon
                name="shield"
                size={14}
                color={leaderboardScope === "local" ? Colors.primary : "#64748B"}
              />
              <Text
                style={[
                  styles.scopeBtnText,
                  leaderboardScope === "local" && styles.scopeBtnTextActive,
                ]}
              >
                {`Lokal ${entityType}`}
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[
                styles.scopeBtn,
                leaderboardScope === "global" && styles.scopeBtnActive,
              ]}
              onPress={() => setLeaderboardScope("global")}
              activeOpacity={0.8}
            >
              <Icon
                name="globe"
                size={14}
                color={leaderboardScope === "global" ? Colors.primary : "#64748B"}
              />
              <Text
                style={[
                  styles.scopeBtnText,
                  leaderboardScope === "global" && styles.scopeBtnTextActive,
                ]}
              >
                Global Semua
              </Text>
            </TouchableOpacity>
          </View>

          {/* Period Filter (Bulan Ini, Tahun Ini, Tahun Lalu) & Unit Toggle */}
          <View style={styles.leaderboardFilterContainer}>
            <View style={styles.periodFilterRow}>
              <TouchableOpacity
                style={[
                  styles.periodBtn,
                  leaderboardPeriod === "month" && styles.periodBtnActive,
                ]}
                onPress={() => setLeaderboardPeriod("month")}
                activeOpacity={0.8}
              >
                <Text
                  style={[
                    styles.periodBtnText,
                    leaderboardPeriod === "month" && styles.periodBtnTextActive,
                  ]}
                >
                  Bulan Ini
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[
                  styles.periodBtn,
                  leaderboardPeriod === "year" && styles.periodBtnActive,
                ]}
                onPress={() => setLeaderboardPeriod("year")}
                activeOpacity={0.8}
              >
                <Text
                  style={[
                    styles.periodBtnText,
                    leaderboardPeriod === "year" && styles.periodBtnTextActive,
                  ]}
                >
                  Tahun Ini
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[
                  styles.periodBtn,
                  leaderboardPeriod === "last_year" && styles.periodBtnActive,
                ]}
                onPress={() => setLeaderboardPeriod("last_year")}
                activeOpacity={0.8}
              >
                <Text
                  style={[
                    styles.periodBtnText,
                    leaderboardPeriod === "last_year" && styles.periodBtnTextActive,
                  ]}
                >
                  Tahun Lalu
                </Text>
              </TouchableOpacity>
            </View>

            <View style={styles.unitToggleGroupLeaderboard}>
              <TouchableOpacity
                style={[
                  styles.unitToggleBtn,
                  unit === "km" && styles.unitToggleBtnActive,
                ]}
                onPress={() => handleToggleUnit("km")}
                activeOpacity={0.8}
              >
                <Text
                  style={[
                    styles.unitToggleText,
                    unit === "km" && styles.unitToggleTextActive,
                  ]}
                >
                  KM
                </Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={[
                  styles.unitToggleBtn,
                  unit === "mil" && styles.unitToggleBtnActive,
                ]}
                onPress={() => handleToggleUnit("mil")}
                activeOpacity={0.8}
              >
                <Text
                  style={[
                    styles.unitToggleText,
                    unit === "mil" && styles.unitToggleTextActive,
                  ]}
                >
                  MIL
                </Text>
              </TouchableOpacity>
            </View>
          </View>

          {loadingLeaderboard ? (
            <ActivityIndicator size="large" color={Colors.primary} style={{ marginVertical: 36 }} />
          ) : leaderboardData.length === 0 ? (
            <View style={styles.emptyContainer}>
              <Icon name="award" size={48} color={Colors.muted} />
              <Text style={styles.emptyTitle}>Belum Ada Data Peringkat</Text>
              <Text style={styles.emptySubtitle}>
                {leaderboardScope === "local"
                  ? `Belum ada aktivitas lari yang tercatat di ${entityType.toLowerCase()} Anda pada periode ini.`
                  : "Belum ada aktivitas lari secara global pada periode ini."}
              </Text>
            </View>
          ) : (
            <>
              {/* TOP 3 PODIUM LUXURY CARD (Sesuai Desain Referensi) */}
              {(() => {
                const rank1 = leaderboardData.find((i) => i.rank === 1);
                const rank2 = leaderboardData.find((i) => i.rank === 2);
                const rank3 = leaderboardData.find((i) => i.rank === 3);
                const top4to10 = leaderboardData.filter((i) => i.rank > 3).slice(0, 7);

                return (
                  <>
                    <View style={styles.podiumDarkCard}>
                      {/* Ambient Header */}
                      <View style={styles.podiumHeaderRow}>
                        <Icon name="award" size={16} color="#F59E0B" />
                        <Text style={styles.podiumHeaderTitle}>Podium Kejuaraan</Text>
                      </View>
                      <Text style={styles.podiumSubtitle}>
                        {leaderboardScope === "local" ? `Peringkat ${entityType}` : "Peringkat Global"} &bull;{" "}
                        {leaderboardPeriod === "month"
                          ? "Bulan Ini"
                          : leaderboardPeriod === "year"
                            ? "Tahun Ini"
                            : "Tahun Lalu"}
                      </Text>

                      <View style={styles.podiumFramingContainer}>
                        {/* Laurel Wreaths Framing the Podium */}
                        <View style={styles.laurelLeftWrapper} pointerEvents="none">
                          <LaurelWreathLeft size={115} />
                        </View>
                        <View style={styles.laurelRightWrapper} pointerEvents="none">
                          <LaurelWreathRight size={115} />
                        </View>

                        {/* Podium Columns: #2 (Kiri), #1 (Tengah), #3 (Kanan) */}
                        <View style={styles.podiumColumnsRow}>
                          {/* RANK 2 - SILVER (KIRI) */}
                          <View style={styles.podiumCol}>
                            {rank2 ? (
                              <>
                                <View style={styles.avatarPodiumWrapper}>
                                  <View style={styles.avatarRingSilver}>
                                    {rank2.avatar ? (
                                      <Image source={{ uri: rank2.avatar }} style={styles.podiumAvatarImg} />
                                    ) : (
                                      <Text style={styles.podiumAvatarInitials}>
                                        {rank2.name?.charAt(0) || "2"}
                                      </Text>
                                    )}
                                  </View>
                                  <View style={[styles.podiumRankBadge, styles.podiumRankBadgeSilver]}>
                                    <Text style={styles.podiumRankBadgeText}>2</Text>
                                  </View>
                                </View>

                                <Text style={styles.podiumUserName} numberOfLines={1}>
                                  {rank2.name}
                                </Text>
                                <Text style={styles.podiumUserSub} numberOfLines={1}>
                                  {leaderboardScope === "local" ? (rank2.divisi || "Umum") : (rank2.instansi || "Instansi")}
                                </Text>

                                <Text style={[styles.podiumDistanceText, styles.podiumDistanceSilver]}>
                                  {formatLeaderboardDistance(rank2.total_distance_km, unit)}
                                </Text>

                                <View style={styles.podiumStatsBox}>
                                  <Text style={styles.podiumStatLabel}>
                                    ⚡ {formatLeaderboardPace(rank2.best_pace_seconds, unit)}
                                  </Text>
                                  <Text style={styles.podiumStatLabel}>
                                    ⏱️ {rank2.formatted_duration || formatLeaderboardDuration(rank2.total_duration_seconds)}
                                  </Text>
                                </View>

                                <View style={[styles.pedestalBlock, styles.pedestalStepSilver]}>
                                  <Icon name="award" size={18} color="#CBD5E1" />
                                  <Text style={[styles.pedestalRankText, { color: "#CBD5E1" }]}>JUARA 2</Text>
                                </View>
                              </>
                            ) : (
                              <View style={[styles.pedestalBlock, styles.pedestalStepSilver, { opacity: 0.3 }]} />
                            )}
                          </View>

                          {/* RANK 1 - GOLD (TENGAH, TERTINGGI) */}
                          <View style={[styles.podiumCol, styles.podiumColCenter]}>
                            {rank1 ? (
                              <>
                                <View style={styles.avatarPodiumWrapper}>
                                  <View style={styles.avatarRingGold}>
                                    {rank1.avatar ? (
                                      <Image source={{ uri: rank1.avatar }} style={styles.podiumAvatarImg} />
                                    ) : (
                                      <Text style={[styles.podiumAvatarInitials, { color: "#FDE68A" }]}>
                                        {rank1.name?.charAt(0) || "1"}
                                      </Text>
                                    )}
                                  </View>
                                  <View style={[styles.podiumRankBadge, styles.podiumRankBadgeGold]}>
                                    <Text style={styles.podiumRankBadgeText}>1</Text>
                                  </View>
                                </View>

                                <Text style={[styles.podiumUserName, styles.podiumUserNameGold]} numberOfLines={1}>
                                  {rank1.name}
                                </Text>
                                <Text style={[styles.podiumUserSub, { color: "#FDE68A" }]} numberOfLines={1}>
                                  {leaderboardScope === "local" ? (rank1.divisi || "Umum") : (rank1.instansi || "Instansi")}
                                </Text>

                                <Text style={[styles.podiumDistanceText, styles.podiumDistanceGold]}>
                                  {formatLeaderboardDistance(rank1.total_distance_km, unit)}
                                </Text>

                                <View style={[styles.podiumStatsBox, { borderColor: "rgba(245, 158, 11, 0.4)", backgroundColor: "rgba(245, 158, 11, 0.14)" }]}>
                                  <Text style={[styles.podiumStatLabel, { color: "#FDE68A" }]}>
                                    ⚡ {formatLeaderboardPace(rank1.best_pace_seconds, unit)}
                                  </Text>
                                  <Text style={[styles.podiumStatLabel, { color: "#FDE68A" }]}>
                                    ⏱️ {rank1.formatted_duration || formatLeaderboardDuration(rank1.total_duration_seconds)}
                                  </Text>
                                </View>

                                <View style={[styles.pedestalBlock, styles.pedestalStepGold]}>
                                  <Icon name="award" size={22} color="#F59E0B" />
                                  <Text style={[styles.pedestalRankText, { color: "#FDE68A" }]}>JUARA 1</Text>
                                </View>
                              </>
                            ) : null}
                          </View>

                          {/* RANK 3 - BRONZE (KANAN) */}
                          <View style={styles.podiumCol}>
                            {rank3 ? (
                              <>
                                <View style={styles.avatarPodiumWrapper}>
                                  <View style={styles.avatarRingBronze}>
                                    {rank3.avatar ? (
                                      <Image source={{ uri: rank3.avatar }} style={styles.podiumAvatarImg} />
                                    ) : (
                                      <Text style={styles.podiumAvatarInitials}>
                                        {rank3.name?.charAt(0) || "3"}
                                      </Text>
                                    )}
                                  </View>
                                  <View style={[styles.podiumRankBadge, styles.podiumRankBadgeBronze]}>
                                    <Text style={styles.podiumRankBadgeText}>3</Text>
                                  </View>
                                </View>

                                <Text style={styles.podiumUserName} numberOfLines={1}>
                                  {rank3.name}
                                </Text>
                                <Text style={styles.podiumUserSub} numberOfLines={1}>
                                  {leaderboardScope === "local" ? (rank3.divisi || "Umum") : (rank3.instansi || "Instansi")}
                                </Text>

                                <Text style={[styles.podiumDistanceText, styles.podiumDistanceBronze]}>
                                  {formatLeaderboardDistance(rank3.total_distance_km, unit)}
                                </Text>

                                <View style={styles.podiumStatsBox}>
                                  <Text style={styles.podiumStatLabel}>
                                    ⚡ {formatLeaderboardPace(rank3.best_pace_seconds, unit)}
                                  </Text>
                                  <Text style={styles.podiumStatLabel}>
                                    ⏱️ {rank3.formatted_duration || formatLeaderboardDuration(rank3.total_duration_seconds)}
                                  </Text>
                                </View>

                                <View style={[styles.pedestalBlock, styles.pedestalStepBronze]}>
                                  <Icon name="award" size={17} color="#FDBA74" />
                                  <Text style={[styles.pedestalRankText, { color: "#FDBA74" }]}>JUARA 3</Text>
                                </View>
                              </>
                            ) : (
                              <View style={[styles.pedestalBlock, styles.pedestalStepBronze, { opacity: 0.3 }]} />
                            )}
                          </View>
                        </View>
                      </View>
                    </View>

                    {/* TOP 4 - 10 LIST (Sisanya Top 10 Seperti List Biasa) */}
                    {top4to10.length > 0 && (
                      <>
                        <View style={styles.listSectionHeader}>
                          <Text style={styles.listSectionTitle}>Peringkat 4 &ndash; 10</Text>
                          <Text style={styles.listSectionBadge}>{top4to10.length} Pelari</Text>
                        </View>

                        {top4to10.map((item) => (
                          <View
                            key={item.user_id}
                            style={[
                              styles.leaderboardCard,
                              item.is_current_user && styles.leaderboardCardSelf,
                            ]}
                          >
                            <View style={styles.rankPill}>
                              <Text style={styles.rankPillText}>#{item.rank}</Text>
                            </View>

                            <View style={styles.rowAvatar}>
                              {item.avatar ? (
                                <Image source={{ uri: item.avatar }} style={styles.rowAvatarImg} />
                              ) : (
                                <Text style={styles.rowAvatarText}>
                                  {item.name?.charAt(0) || "U"}
                                </Text>
                              )}
                            </View>

                            <View style={{ flex: 1 }}>
                              <Text
                                style={[
                                  styles.leaderboardName,
                                  item.is_current_user && { color: Colors.primary, fontWeight: "bold" },
                                ]}
                                numberOfLines={1}
                              >
                                {item.name} {item.is_current_user ? "(Anda)" : ""}
                              </Text>
                              <View style={styles.leaderboardSubRow}>
                                <Text style={styles.leaderboardSubText}>
                                  {leaderboardScope === "local" ? item.divisi : item.instansi}
                                </Text>
                                <Text style={styles.leaderboardSubText}>&bull;</Text>
                                <Text style={[styles.leaderboardSubText, { color: "#059669", fontWeight: "600" }]}>
                                  ⚡ {formatLeaderboardPace(item.best_pace_seconds, unit)}
                                </Text>
                                <Text style={styles.leaderboardSubText}>&bull;</Text>
                                <Text style={styles.leaderboardSubText}>
                                  ⏱️ {item.formatted_duration || formatLeaderboardDuration(item.total_duration_seconds)}
                                </Text>
                              </View>
                            </View>

                            <View style={{ alignItems: "flex-end", marginLeft: 8 }}>
                              <Text style={styles.leaderboardDistance}>
                                {formatLeaderboardDistance(item.total_distance_km, unit)}
                              </Text>
                              <Text style={styles.leaderboardSessions}>
                                {item.total_sessions || 0} Sesi
                              </Text>
                            </View>
                          </View>
                        ))}
                      </>
                    )}
                  </>
                );
              })()}
            </>
          )}
        </View>
      )}

      {/* FINISH CELEBRATION MODAL */}
      <Modal visible={showFinishModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.finishModalCard}>
            <View style={styles.finishBadge}>
              <Icon name="check" size={32} color="#fff" />
            </View>

            <Text style={styles.finishModalTitle}>Sesi Berhasil Selesai!</Text>
            <Text style={styles.finishModalSubtitle}>
              Keren! Anda telah menyelesaikan sesi {activityType === "run" ? "lari" : "olahraga"}.
            </Text>

            {/* Modal Unit Selector */}
            <View style={styles.modalUnitRow}>
              <Text style={styles.modalUnitLabel}>Format Pengukuran:</Text>
              <View style={styles.unitToggleGroup}>
                <TouchableOpacity
                  style={[
                    styles.unitToggleBtn,
                    unit === "km" && styles.unitToggleBtnActive,
                  ]}
                  onPress={() => handleToggleUnit("km")}
                >
                  <Text
                    style={[
                      styles.unitToggleText,
                      unit === "km" && styles.unitToggleTextActive,
                    ]}
                  >
                    KM
                  </Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={[
                    styles.unitToggleBtn,
                    unit === "mil" && styles.unitToggleBtnActive,
                  ]}
                  onPress={() => handleToggleUnit("mil")}
                >
                  <Text
                    style={[
                      styles.unitToggleText,
                      unit === "mil" && styles.unitToggleTextActive,
                    ]}
                  >
                    MIL
                  </Text>
                </TouchableOpacity>
              </View>
            </View>

            <View style={styles.finishStatsGrid}>
              <View style={styles.finishStatBox}>
                <Text style={styles.finishStatLabel}>Jarak Tempuh</Text>
                <Text style={styles.finishStatValue}>{displayDistance} {unit.toUpperCase()}</Text>
              </View>

              <View style={styles.finishStatBox}>
                <Text style={styles.finishStatLabel}>Durasi</Text>
                <Text style={styles.finishStatValue}>{formatTimer(seconds)}</Text>
              </View>

              <View style={styles.finishStatBox}>
                <Text style={styles.finishStatLabel}>Pace Rata-rata</Text>
                <Text style={styles.finishStatValue}>{currentPace}/{unit}</Text>
              </View>

              <View style={styles.finishStatBox}>
                <Text style={styles.finishStatLabel}>Kalori</Text>
                <Text style={styles.finishStatValue}>{Math.round(calories)} kcal</Text>
              </View>
            </View>

            <TouchableOpacity
              style={styles.saveModalBtn}
              activeOpacity={0.8}
              onPress={handleSaveActivity}
              disabled={isSaving}
            >
              {isSaving ? (
                <ActivityIndicator color="#fff" />
              ) : (
                <Text style={styles.saveModalBtnText}>Simpan ke Riwayat & Leaderboard</Text>
              )}
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.discardModalBtn}
              onPress={() => {
                setShowFinishModal(false);
                setIsRunning(false);
                setSeconds(0);
              }}
            >
              <Text style={styles.discardModalBtnText}>Buang Sesi Ini</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#F8FAFC",
  },
  tabHeader: {
    flexDirection: "row",
    backgroundColor: "#fff",
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderBottomWidth: 1,
    borderBottomColor: "#E2E8F0",
  },
  tabButton: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 10,
    borderRadius: 12,
    gap: 6,
  },
  tabButtonActive: {
    backgroundColor: "#ECFDF5",
  },
  tabButtonText: {
    fontSize: 13,
    fontWeight: "600",
    color: "#64748B",
  },
  tabButtonTextActive: {
    color: Colors.primary,
    fontWeight: "bold",
  },
  scrollContent: {
    padding: 16,
    paddingBottom: 100,
  },
  modeContainer: {
    flexDirection: "row",
    backgroundColor: "#E2E8F0",
    borderRadius: 14,
    padding: 4,
    marginBottom: 16,
  },
  modePill: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 8,
    borderRadius: 10,
    gap: 6,
  },
  modePillActive: {
    backgroundColor: Colors.primary,
  },
  modePillText: {
    fontSize: 13,
    fontWeight: "600",
    color: "#64748B",
  },
  modePillTextActive: {
    color: "#fff",
  },
  dashboardCard: {
    backgroundColor: "#fff",
    borderRadius: 24,
    padding: 20,
    alignItems: "center",
    borderWidth: 1,
    borderColor: "#E2E8F0",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 2,
    marginBottom: 20,
  },
  dashboardHeaderRow: {
    flexDirection: "row",
    width: "100%",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 12,
  },
  statusIndicatorBox: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
  },
  statusPillDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: "#94A3B8",
  },
  dashboardLabel: {
    fontSize: 11.5,
    fontWeight: "700",
    letterSpacing: 0.5,
    color: "#334155",
  },
  unitToggleGroup: {
    flexDirection: "row",
    backgroundColor: "#F1F5F9",
    borderRadius: 12,
    padding: 2,
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  unitToggleBtn: {
    paddingHorizontal: 12,
    paddingVertical: 5,
    borderRadius: 10,
  },
  unitToggleBtnActive: {
    backgroundColor: Colors.primary,
    shadowColor: Colors.primary,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 4,
    elevation: 2,
  },
  unitToggleText: {
    fontSize: 11,
    fontWeight: "700",
    color: "#64748B",
  },
  unitToggleTextActive: {
    color: "#FFFFFF",
  },
  distanceRow: {
    flexDirection: "row",
    alignItems: "baseline",
    justifyContent: "center",
    marginBottom: 16,
  },
  bigDistanceNumber: {
    fontSize: 62,
    fontWeight: "900",
    color: "#0F172A",
    letterSpacing: -2,
  },
  distanceUnitBadge: {
    marginLeft: 8,
    backgroundColor: "#ECFDF5",
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#A7F3D0",
  },
  distanceUnit: {
    fontSize: 14,
    fontWeight: "800",
    color: Colors.primary,
    letterSpacing: 0.5,
  },
  metricsGrid: {
    flexDirection: "row",
    width: "100%",
    paddingVertical: 14,
    borderTopWidth: 1,
    borderBottomWidth: 1,
    borderColor: "#F1F5F9",
    marginBottom: 14,
  },
  metricItem: {
    flex: 1,
    alignItems: "center",
  },
  metricLabel: {
    fontSize: 9.5,
    fontWeight: "700",
    color: "#94A3B8",
    marginBottom: 4,
    letterSpacing: 0.5,
  },
  metricValue: {
    fontSize: 15.5,
    fontWeight: "800",
    color: "#1E293B",
  },
  stepsBadge: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#ECFDF5",
    paddingHorizontal: 14,
    paddingVertical: 6,
    borderRadius: 20,
    gap: 6,
  },
  stepsBadgeText: {
    fontSize: 12,
    fontWeight: "600",
    color: Colors.primary,
  },
  actionContainer: {
    marginBottom: 20,
    alignItems: "center",
  },
  startBigButton: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: Colors.primary,
    width: "100%",
    paddingVertical: 18,
    borderRadius: 20,
    gap: 10,
    shadowColor: Colors.primary,
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.3,
    shadowRadius: 12,
    elevation: 4,
  },
  startBigButtonText: {
    fontSize: 16,
    fontWeight: "800",
    color: "#fff",
    letterSpacing: 0.5,
  },
  controlRow: {
    flexDirection: "row",
    gap: 20,
    justifyContent: "center",
    width: "100%",
  },
  circleButton: {
    width: 80,
    height: 80,
    borderRadius: 40,
    alignItems: "center",
    justifyContent: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.15,
    shadowRadius: 8,
    elevation: 3,
  },
  circleBtnText: {
    color: "#fff",
    fontSize: 11,
    fontWeight: "700",
    marginTop: 4,
  },
  tipsCard: {
    flexDirection: "row",
    backgroundColor: "#F0FDF4",
    borderWidth: 1,
    borderColor: "#DCFCE7",
    padding: 16,
    borderRadius: 18,
    gap: 12,
  },
  tipsIconBox: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: "#DCFCE7",
    alignItems: "center",
    justifyContent: "center",
  },
  tipsTitle: {
    fontSize: 13,
    fontWeight: "700",
    color: "#166534",
    marginBottom: 4,
  },
  tipsBody: {
    fontSize: 12,
    color: "#15803D",
    lineHeight: 18,
  },
  historyStatsCard: {
    backgroundColor: Colors.primary,
    borderRadius: 20,
    padding: 18,
    marginBottom: 20,
  },
  historyHeaderRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 16,
  },
  historySummaryTitle: {
    fontSize: 13,
    fontWeight: "700",
    color: "#A7F3D0",
    letterSpacing: 0.5,
  },
  unitToggleGroupLight: {
    flexDirection: "row",
    backgroundColor: "rgba(255, 255, 255, 0.2)",
    borderRadius: 10,
    padding: 2,
  },
  unitToggleBtnLight: {
    paddingHorizontal: 10,
    paddingVertical: 3,
    borderRadius: 8,
  },
  unitToggleBtnActiveLight: {
    backgroundColor: "#FFFFFF",
  },
  unitToggleTextLight: {
    fontSize: 10.5,
    fontWeight: "700",
    color: "#E2E8F0",
  },
  unitToggleTextActiveLight: {
    color: Colors.primary,
  },
  statBoxesRow: {
    flexDirection: "row",
  },
  statBox: {
    flex: 1,
    alignItems: "center",
  },
  statNumber: {
    fontSize: 22,
    fontWeight: "900",
    color: "#fff",
  },
  statLabel: {
    fontSize: 11,
    fontWeight: "600",
    color: "#A7F3D0",
    marginTop: 2,
  },
  statDivider: {
    width: 1,
    backgroundColor: "rgba(255,255,255,0.2)",
  },
  sectionHeaderTitle: {
    fontSize: 15,
    fontWeight: "800",
    color: "#1E293B",
    marginBottom: 12,
  },
  activityItemCard: {
    backgroundColor: "#fff",
    borderRadius: 18,
    padding: 16,
    marginBottom: 12,
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  activityHeader: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 12,
  },
  activityIconCircle: {
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: "#ECFDF5",
    alignItems: "center",
    justifyContent: "center",
  },
  activityTitle: {
    fontSize: 14,
    fontWeight: "700",
    color: "#0F172A",
  },
  activityDate: {
    fontSize: 11,
    color: "#94A3B8",
    marginTop: 2,
  },
  activityKmBadge: {
    fontSize: 15,
    fontWeight: "800",
    color: Colors.primary,
  },
  activityDetailRow: {
    flexDirection: "row",
    backgroundColor: "#F8FAFC",
    borderRadius: 12,
    paddingVertical: 10,
    paddingHorizontal: 12,
  },
  activityDetailCol: {
    flex: 1,
    alignItems: "center",
  },
  detailLabel: {
    fontSize: 10,
    color: "#94A3B8",
    fontWeight: "600",
    marginBottom: 2,
  },
  detailValue: {
    fontSize: 13,
    fontWeight: "700",
    color: "#334155",
  },
  emptyContainer: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 40,
  },
  emptyTitle: {
    fontSize: 16,
    fontWeight: "700",
    color: "#475569",
    marginTop: 12,
  },
  emptySubtitle: {
    fontSize: 12,
    color: "#94A3B8",
    textAlign: "center",
    marginTop: 4,
    paddingHorizontal: 30,
  },
  // Scope Filter (Lokal vs Global)
  scopeFilterRow: {
    flexDirection: "row",
    backgroundColor: "#E2E8F0",
    borderRadius: 14,
    padding: 3,
    marginBottom: 10,
  },
  scopeBtn: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 9,
    borderRadius: 11,
    gap: 6,
  },
  scopeBtnActive: {
    backgroundColor: "#FFFFFF",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 2,
  },
  scopeBtnText: {
    fontSize: 12.5,
    fontWeight: "600",
    color: "#64748B",
  },
  scopeBtnTextActive: {
    color: Colors.primary,
    fontWeight: "bold",
  },

  // Period Filter & Unit
  leaderboardFilterContainer: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 16,
    gap: 8,
  },
  periodFilterRow: {
    flex: 1,
    flexDirection: "row",
    backgroundColor: "#E2E8F0",
    borderRadius: 12,
    padding: 3,
  },
  periodBtn: {
    flex: 1,
    paddingVertical: 7,
    alignItems: "center",
    borderRadius: 9,
  },
  periodBtnActive: {
    backgroundColor: "#fff",
  },
  periodBtnText: {
    fontSize: 11.5,
    fontWeight: "600",
    color: "#64748B",
  },
  periodBtnTextActive: {
    color: Colors.primary,
    fontWeight: "bold",
  },
  unitToggleGroupLeaderboard: {
    flexDirection: "row",
    backgroundColor: "#E2E8F0",
    borderRadius: 12,
    padding: 3,
  },

  // Dark Luxury Podium Card (Matching Reference Image)
  podiumDarkCard: {
    backgroundColor: "#0B132B",
    borderRadius: 24,
    paddingTop: 18,
    paddingHorizontal: 8,
    paddingBottom: 0,
    marginBottom: 20,
    borderWidth: 1,
    borderColor: "#1E293B",
    shadowColor: "#F59E0B",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.2,
    shadowRadius: 16,
    elevation: 6,
    overflow: "hidden",
  },
  podiumHeaderRow: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 6,
    marginBottom: 4,
  },
  podiumHeaderTitle: {
    fontSize: 14,
    fontWeight: "900",
    color: "#FDE68A",
    letterSpacing: 1.2,
    textTransform: "uppercase",
  },
  podiumSubtitle: {
    fontSize: 11,
    color: "#94A3B8",
    textAlign: "center",
    marginBottom: 16,
  },
  podiumFramingContainer: {
    flexDirection: "row",
    alignItems: "flex-end",
    justifyContent: "space-between",
    position: "relative",
  },
  laurelLeftWrapper: {
    position: "absolute",
    left: -4,
    bottom: 30,
    zIndex: 1,
    opacity: 0.85,
  },
  laurelRightWrapper: {
    position: "absolute",
    right: -4,
    bottom: 30,
    zIndex: 1,
    opacity: 0.85,
  },
  podiumColumnsRow: {
    flex: 1,
    flexDirection: "row",
    alignItems: "flex-end",
    justifyContent: "center",
    gap: 6,
    paddingHorizontal: 8,
    zIndex: 2,
  },
  podiumCol: {
    flex: 1,
    alignItems: "center",
    justifyContent: "flex-end",
  },
  podiumColCenter: {
    flex: 1.15,
    zIndex: 3,
  },
  avatarPodiumWrapper: {
    position: "relative",
    marginBottom: 8,
    alignItems: "center",
    justifyContent: "center",
  },
  avatarRingGold: {
    width: 64,
    height: 64,
    borderRadius: 32,
    borderWidth: 3,
    borderColor: "#F59E0B",
    backgroundColor: "#1E293B",
    alignItems: "center",
    justifyContent: "center",
    shadowColor: "#F59E0B",
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.7,
    shadowRadius: 10,
    elevation: 5,
  },
  avatarRingSilver: {
    width: 54,
    height: 54,
    borderRadius: 27,
    borderWidth: 2.5,
    borderColor: "#CBD5E1",
    backgroundColor: "#1E293B",
    alignItems: "center",
    justifyContent: "center",
    shadowColor: "#CBD5E1",
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.5,
    shadowRadius: 8,
    elevation: 4,
  },
  avatarRingBronze: {
    width: 50,
    height: 50,
    borderRadius: 25,
    borderWidth: 2.5,
    borderColor: "#D97706",
    backgroundColor: "#1E293B",
    alignItems: "center",
    justifyContent: "center",
    shadowColor: "#D97706",
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.5,
    shadowRadius: 8,
    elevation: 4,
  },
  podiumAvatarImg: {
    width: "100%",
    height: "100%",
    borderRadius: 32,
  },
  podiumAvatarInitials: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#F8FAFC",
  },
  podiumRankBadge: {
    position: "absolute",
    top: -6,
    right: -4,
    width: 22,
    height: 22,
    borderRadius: 11,
    alignItems: "center",
    justifyContent: "center",
    borderWidth: 1.5,
    borderColor: "#0B132B",
  },
  podiumRankBadgeGold: {
    backgroundColor: "#F59E0B",
  },
  podiumRankBadgeSilver: {
    backgroundColor: "#94A3B8",
  },
  podiumRankBadgeBronze: {
    backgroundColor: "#B45309",
  },
  podiumRankBadgeText: {
    fontSize: 10.5,
    fontWeight: "900",
    color: "#FFFFFF",
  },
  podiumUserName: {
    fontSize: 11.5,
    fontWeight: "700",
    color: "#FFFFFF",
    textAlign: "center",
    maxWidth: 90,
  },
  podiumUserNameGold: {
    fontSize: 13,
    fontWeight: "800",
  },
  podiumUserSub: {
    fontSize: 9,
    color: "#94A3B8",
    textAlign: "center",
    marginBottom: 4,
    maxWidth: 85,
  },
  podiumDistanceText: {
    fontSize: 13,
    fontWeight: "800",
    textAlign: "center",
    marginBottom: 4,
  },
  podiumDistanceGold: {
    fontSize: 14.5,
    fontWeight: "900",
    color: "#FCD34D",
  },
  podiumDistanceSilver: {
    color: "#E2E8F0",
  },
  podiumDistanceBronze: {
    color: "#FDBA74",
  },
  podiumStatsBox: {
    backgroundColor: "rgba(30, 41, 59, 0.75)",
    paddingVertical: 3,
    paddingHorizontal: 5,
    borderRadius: 8,
    alignItems: "center",
    marginBottom: 8,
    gap: 1.5,
    borderWidth: 1,
    borderColor: "rgba(255, 255, 255, 0.08)",
  },
  podiumStatLabel: {
    fontSize: 8.5,
    fontWeight: "700",
    color: "#CBD5E1",
  },
  pedestalBlock: {
    width: "100%",
    backgroundColor: "#131D31",
    borderTopLeftRadius: 10,
    borderTopRightRadius: 10,
    alignItems: "center",
    justifyContent: "flex-start",
    paddingTop: 8,
    borderWidth: 1,
    borderColor: "rgba(255, 255, 255, 0.08)",
    borderBottomWidth: 0,
  },
  pedestalStepGold: {
    height: 100,
    backgroundColor: "#172554",
    borderTopColor: "#F59E0B",
    borderTopWidth: 3,
  },
  pedestalStepSilver: {
    height: 75,
    backgroundColor: "#1E293B",
    borderTopColor: "#94A3B8",
    borderTopWidth: 3,
  },
  pedestalStepBronze: {
    height: 58,
    backgroundColor: "#1A2238",
    borderTopColor: "#B45309",
    borderTopWidth: 3,
  },
  pedestalRankText: {
    fontSize: 9,
    fontWeight: "700",
    letterSpacing: 0.5,
    marginTop: 2,
  },

  // Rest of List (Top 4 - 10)
  listSectionHeader: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    marginBottom: 12,
    marginTop: 4,
  },
  listSectionTitle: {
    fontSize: 14,
    fontWeight: "700",
    color: "#1E293B",
  },
  listSectionBadge: {
    fontSize: 11,
    fontWeight: "600",
    color: "#64748B",
  },
  leaderboardCard: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#FFFFFF",
    borderRadius: 16,
    padding: 12,
    marginBottom: 9,
    borderWidth: 1,
    borderColor: "#E2E8F0",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.04,
    shadowRadius: 4,
    elevation: 1,
  },
  leaderboardCardSelf: {
    borderColor: Colors.primary,
    backgroundColor: "#ECFDF5",
    borderWidth: 1.5,
  },
  rankPill: {
    width: 28,
    height: 28,
    borderRadius: 14,
    backgroundColor: "#F1F5F9",
    alignItems: "center",
    justifyContent: "center",
    marginRight: 10,
  },
  rankPillText: {
    fontSize: 12,
    fontWeight: "800",
    color: "#64748B",
  },
  rowAvatar: {
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: "#E2E8F0",
    alignItems: "center",
    justifyContent: "center",
    marginRight: 11,
    overflow: "hidden",
  },
  rowAvatarText: {
    fontSize: 14,
    fontWeight: "bold",
    color: "#475569",
  },
  rowAvatarImg: {
    width: "100%",
    height: "100%",
  },
  leaderboardName: {
    fontSize: 13.5,
    fontWeight: "700",
    color: "#1E293B",
  },
  leaderboardSubRow: {
    flexDirection: "row",
    alignItems: "center",
    flexWrap: "wrap",
    gap: 6,
    marginTop: 2.5,
  },
  leaderboardSubText: {
    fontSize: 10.5,
    color: "#64748B",
  },
  leaderboardDistance: {
    fontSize: 15,
    fontWeight: "800",
    color: Colors.primary,
  },
  leaderboardSessions: {
    fontSize: 10,
    color: "#94A3B8",
    marginTop: 2,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(0,0,0,0.6)",
    justifyContent: "center",
    alignItems: "center",
    padding: 20,
  },
  finishModalCard: {
    width: "100%",
    backgroundColor: "#fff",
    borderRadius: 28,
    padding: 24,
    alignItems: "center",
  },
  finishBadge: {
    width: 64,
    height: 64,
    borderRadius: 32,
    backgroundColor: Colors.primary,
    alignItems: "center",
    justifyContent: "center",
    marginBottom: 16,
    shadowColor: Colors.primary,
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.3,
    shadowRadius: 10,
    elevation: 4,
  },
  finishModalTitle: {
    fontSize: 20,
    fontWeight: "800",
    color: "#0F172A",
  },
  finishModalSubtitle: {
    fontSize: 12,
    color: "#64748B",
    textAlign: "center",
    marginTop: 4,
    marginBottom: 16,
  },
  modalUnitRow: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    width: "100%",
    backgroundColor: "#F8FAFC",
    paddingHorizontal: 14,
    paddingVertical: 8,
    borderRadius: 14,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  modalUnitLabel: {
    fontSize: 12,
    fontWeight: "600",
    color: "#475569",
  },
  finishStatsGrid: {
    flexDirection: "row",
    flexWrap: "wrap",
    gap: 10,
    width: "100%",
    marginBottom: 24,
  },
  finishStatBox: {
    width: (SCREEN_WIDTH - 98) / 2,
    backgroundColor: "#F8FAFC",
    borderRadius: 14,
    padding: 12,
    alignItems: "center",
  },
  finishStatLabel: {
    fontSize: 10,
    color: "#94A3B8",
    fontWeight: "600",
    marginBottom: 4,
  },
  finishStatValue: {
    fontSize: 16,
    fontWeight: "800",
    color: "#1E293B",
  },
  saveModalBtn: {
    width: "100%",
    backgroundColor: Colors.primary,
    paddingVertical: 14,
    borderRadius: 16,
    alignItems: "center",
    marginBottom: 10,
  },
  saveModalBtnText: {
    color: "#fff",
    fontSize: 14,
    fontWeight: "700",
  },
  discardModalBtn: {
    paddingVertical: 10,
  },
  discardModalBtnText: {
    color: Colors.error,
    fontSize: 13,
    fontWeight: "600",
  },
  mapCard: {
    width: "100%",
    height: 240,
    borderRadius: 22,
    overflow: "hidden",
    backgroundColor: "#E2E8F0",
    marginBottom: 16,
    borderWidth: 1,
    borderColor: "#CBD5E1",
    position: "relative",
    shadowColor: "#0F172A",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.08,
    shadowRadius: 10,
    elevation: 3,
  },
  mapWebView: {
    width: "100%",
    height: "100%",
    backgroundColor: "#F1F5F9",
  },
  mapTopFloatingRow: {
    position: "absolute",
    top: 10,
    left: 10,
    right: 10,
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    zIndex: 10,
    elevation: 10,
  },
  gpsBadge: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 9,
    paddingVertical: 5,
    borderRadius: 14,
    gap: 5,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  gpsBadgeOk: {
    backgroundColor: "rgba(255, 255, 255, 0.94)",
  },
  gpsBadgeSearching: {
    backgroundColor: "rgba(254, 243, 199, 0.95)",
  },
  gpsDot: {
    width: 7,
    height: 7,
    borderRadius: 3.5,
  },
  gpsDotOk: {
    backgroundColor: "#16A34A",
  },
  gpsDotSearching: {
    backgroundColor: "#F59E0B",
  },
  gpsBadgeText: {
    fontSize: 10.5,
    fontWeight: "700",
    color: "#1E293B",
  },
  movementBadge: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 9,
    paddingVertical: 5,
    borderRadius: 14,
    gap: 5,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  movementBadgeMoving: {
    backgroundColor: "rgba(236, 253, 245, 0.95)",
    borderWidth: 1,
    borderColor: "#A7F3D0",
  },
  movementBadgeStationary: {
    backgroundColor: "rgba(254, 243, 199, 0.95)",
    borderWidth: 1,
    borderColor: "#FDE68A",
  },
  movementDot: {
    width: 7,
    height: 7,
    borderRadius: 3.5,
  },
  movementDotMoving: {
    backgroundColor: "#10B981",
  },
  movementDotStationary: {
    backgroundColor: "#F59E0B",
  },
  movementBadgeText: {
    fontSize: 10.5,
    fontWeight: "700",
    color: "#1E293B",
  },
  mapModeBadge: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "rgba(255, 255, 255, 0.94)",
    paddingHorizontal: 9,
    paddingVertical: 5,
    borderRadius: 14,
    gap: 5,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  mapModeBadgeText: {
    fontSize: 10.5,
    fontWeight: "700",
  },
  mapRecenterBtn: {
    position: "absolute",
    bottom: 12,
    right: 12,
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: "#FFFFFF",
    alignItems: "center",
    justifyContent: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.18,
    shadowRadius: 6,
    elevation: 4,
    zIndex: 10,
  },
});
