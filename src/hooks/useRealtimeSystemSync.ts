import { useEffect, useRef, useState, useCallback } from 'react';
import { AppState, AppStateStatus, Platform } from 'react-native';
import api from '../api/axiosConfig';
import { Config } from '../settings/Config';
import { SoftwareReleaseData } from '../api/appUpdateService';

interface UseRealtimeSystemSyncOptions {
  onActiveModulesChange?: (modules: string[]) => void;
  onRoleLabelsChange?: (labels: Record<string, string>) => void;
  intervalMs?: number;
  isAuthenticated?: boolean;
}

export interface RealtimeSyncState {
  isMaintenance: boolean;
  maintenanceMessage: string | null;
  isMandatoryUpgrade: boolean;
  forceUpgradeData: SoftwareReleaseData | null;
  activeModules: string[] | null;
  recheckSync: () => Promise<void>;
}

export const useRealtimeSystemSync = (
  options: UseRealtimeSystemSyncOptions = {}
): RealtimeSyncState => {
  const {
    onActiveModulesChange,
    onRoleLabelsChange,
    intervalMs = 6000,
    isAuthenticated = true,
  } = options;

  const [isMaintenance, setIsMaintenance] = useState(false);
  const [maintenanceMessage, setMaintenanceMessage] = useState<string | null>(null);
  const [isMandatoryUpgrade, setIsMandatoryUpgrade] = useState(false);
  const [forceUpgradeData, setForceUpgradeData] = useState<SoftwareReleaseData | null>(null);
  const [activeModules, setActiveModules] = useState<string[] | null>(null);

  const lastEtagRef = useRef<string | null>(null);
  const isCheckingRef = useRef<boolean>(false);

  const syncCheck = useCallback(async () => {
    // 1. Skip check if app is in background or another check is actively running
    if (AppState.currentState !== 'active' || isCheckingRef.current) {
      return;
    }

    isCheckingRef.current = true;

    try {
      const headers: Record<string, string> = {
        'X-App-Version': Config.APP_VERSION,
      };

      if (lastEtagRef.current) {
        headers['If-None-Match'] = lastEtagRef.current;
      }

      const endpoint = isAuthenticated ? '/me/sync' : '/system/realtime-sync';
      const platform = Platform.OS === 'ios' ? 'ios' : 'android';

      const response = await api.get(endpoint, {
        headers,
        params: {
          platform,
          current_version: Config.APP_VERSION,
        },
        timeout: 7000,
        validateStatus: (status) => (status >= 200 && status < 300) || status === 304,
      });

      // 2. HTTP 304 Not Modified:
      // ZERO database queries, ZERO payload bytes transferred, ZERO React re-renders!
      if (response.status === 304) {
        return;
      }

      // 3. HTTP 200 OK:
      // Server detected an update (Admin toggled maintenance, published mandatory update, or changed module permissions)
      if (response.status === 200 && response.data) {
        const data = response.data;
        const newEtag = response.headers?.['etag'] || data?.version;
        if (newEtag) {
          lastEtagRef.current = newEtag;
        }

        // Handle Maintenance Mode
        const maintenanceActive = !!data.maintenance?.is_maintenance;
        setIsMaintenance(maintenanceActive);
        setMaintenanceMessage(data.maintenance?.message || null);

        // Handle Mandatory Force Upgrade
        const updateInfo = data.app_update;
        if (updateInfo?.has_update && updateInfo?.is_mandatory && updateInfo?.data) {
          setIsMandatoryUpgrade(true);
          setForceUpgradeData(updateInfo.data);
        } else {
          setIsMandatoryUpgrade(false);
          setForceUpgradeData(null);
        }

        // Handle Active Feature Modules
        if (Array.isArray(data.active_modules)) {
          setActiveModules(data.active_modules);
          if (onActiveModulesChange) {
            onActiveModulesChange(data.active_modules);
          }
        }

        // Handle Custom Role Labels
        if (data.instansi?.custom_role_labels && onRoleLabelsChange) {
          onRoleLabelsChange(data.instansi.custom_role_labels);
        }
      }
    } catch (err) {
      // Graceful silence on network disconnect
    } finally {
      isCheckingRef.current = false;
    }
  }, [isAuthenticated, onActiveModulesChange, onRoleLabelsChange]);

  useEffect(() => {
    let timer: ReturnType<typeof setInterval> | null = null;
    let isSubscribed = true;

    // Run initial sync check
    syncCheck();

    // Setup periodic polling (Webhook-style conditional 304)
    timer = setInterval(() => {
      if (isSubscribed) {
        syncCheck();
      }
    }, intervalMs);

    // Watch AppState: Pause completely in background, re-trigger immediately on foreground resume
    const appStateSubscription = AppState.addEventListener(
      'change',
      (nextState: AppStateStatus) => {
        if (nextState === 'active') {
          syncCheck();
        }
      }
    );

    return () => {
      isSubscribed = false;
      if (timer) clearInterval(timer);
      appStateSubscription.remove();
    };
  }, [syncCheck, intervalMs]);

  return {
    isMaintenance,
    maintenanceMessage,
    isMandatoryUpgrade,
    forceUpgradeData,
    activeModules,
    recheckSync: syncCheck,
  };
};

export default useRealtimeSystemSync;
