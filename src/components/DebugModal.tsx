import React, { useState, useEffect } from 'react';
import {
  Modal,
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ScrollView,
  Share,
  Platform,
  Alert,
} from 'react-native';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import debugLogger, { NetworkLogItem, ErrorLogItem } from '../services/debugLogger';
import { navigationRef } from '../navigation/rootNavigation';
import { Config } from '../settings/Config';

interface DebugModalProps {
  visible: boolean;
  onClose: () => void;
  initialTab?: 'error' | 'network' | 'info';
}

export const DebugModal: React.FC<DebugModalProps> = ({
  visible,
  onClose,
  initialTab = 'error',
}) => {
  const [activeTab, setActiveTab] = useState<'error' | 'network' | 'info'>(initialTab);
  const [networkLogs, setNetworkLogs] = useState<NetworkLogItem[]>([]);
  const [errorLogs, setErrorLogs] = useState<ErrorLogItem[]>([]);
  const [selectedNetworkItem, setSelectedNetworkItem] = useState<NetworkLogItem | null>(null);

  useEffect(() => {
    const refresh = () => {
      setNetworkLogs(debugLogger.getNetworkLogs());
      setErrorLogs(debugLogger.getErrorLogs());
    };

    refresh();
    const unsubscribe = debugLogger.subscribe(refresh);
    return () => unsubscribe();
  }, []);

  const handleShare = async () => {
    try {
      const summary = debugLogger.getDiagnosticSummary();
      await Share.share({
        title: 'Diagnostik Debug Aku Sehat',
        message: summary,
      });
    } catch (e: any) {
      Alert.alert('Gagal Membagikan', e?.message || 'Tidak dapat membagikan log');
    }
  };

  const handleGoToLogin = () => {
    onClose();
    if (navigationRef.isReady()) {
      navigationRef.reset({
        index: 0,
        routes: [{ name: 'Login' }],
      });
    }
  };

  const handleClear = () => {
    debugLogger.clearAll();
    setSelectedNetworkItem(null);
  };

  const latestError = errorLogs[0];
  const latestNetError = networkLogs.find((n) => n.isError);

  return (
    <Modal visible={visible} animationType="slide" transparent={false} onRequestClose={onClose}>
      <View style={styles.container}>
        {/* Header */}
        <View style={styles.header}>
          <View style={styles.headerLeft}>
            <Icon name="bug" size={24} color="#f43f5e" />
            <View style={{ marginLeft: 10 }}>
              <Text style={styles.headerTitle}>Debug Activity & Diagnostik</Text>
              <Text style={styles.headerSubtitle}>
                {Config.APP_NAME} v{Config.APP_VERSION} ({Config.APP_BUILD})
              </Text>
            </View>
          </View>
          <TouchableOpacity style={styles.closeBtn} onPress={onClose}>
            <Icon name="close" size={22} color="#94a3b8" />
          </TouchableOpacity>
        </View>

        {/* Tab Navigation */}
        <View style={styles.tabBar}>
          <TouchableOpacity
            style={[styles.tabItem, activeTab === 'error' && styles.tabItemActive]}
            onPress={() => {
              setActiveTab('error');
              setSelectedNetworkItem(null);
            }}
          >
            <Icon
              name="alert-octagon"
              size={16}
              color={activeTab === 'error' ? '#f43f5e' : '#64748b'}
            />
            <Text style={[styles.tabText, activeTab === 'error' && styles.tabTextActive]}>
              Error & 500 {latestNetError || latestError ? '(!)' : ''}
            </Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.tabItem, activeTab === 'network' && styles.tabItemActive]}
            onPress={() => {
              setActiveTab('network');
              setSelectedNetworkItem(null);
            }}
          >
            <Icon
              name="swap-horizontal"
              size={16}
              color={activeTab === 'network' ? '#38bdf8' : '#64748b'}
            />
            <Text style={[styles.tabText, activeTab === 'network' && styles.tabTextActive]}>
              Network ({networkLogs.length})
            </Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.tabItem, activeTab === 'info' && styles.tabItemActive]}
            onPress={() => {
              setActiveTab('info');
              setSelectedNetworkItem(null);
            }}
          >
            <Icon
              name="information"
              size={16}
              color={activeTab === 'info' ? '#a855f7' : '#64748b'}
            />
            <Text style={[styles.tabText, activeTab === 'info' && styles.tabTextActive]}>
              Info Sistem
            </Text>
          </TouchableOpacity>
        </View>

        {/* Content Area */}
        <ScrollView style={styles.content} contentContainerStyle={{ paddingBottom: 100 }}>
          {activeTab === 'error' && (
            <View>
              {/* Box Error 500 / Network Error Terkini */}
              {latestNetError ? (
                <View style={styles.errorBox}>
                  <View style={styles.errorBoxHeader}>
                    <Icon name="server-network-off" size={20} color="#f43f5e" />
                    <Text style={styles.errorBoxTitle}>
                      HTTP {latestNetError.status || 500} — Server Error
                    </Text>
                  </View>

                  <Text style={styles.errorLabel}>Waktu:</Text>
                  <Text style={styles.errorValue}>{latestNetError.timestamp}</Text>

                  <Text style={styles.errorLabel}>Endpoint URL:</Text>
                  <Text style={[styles.errorValue, { color: '#38bdf8' }]}>
                    [{latestNetError.method}] {latestNetError.fullUrl}
                  </Text>

                  {latestNetError.errorMessage && (
                    <>
                      <Text style={styles.errorLabel}>Pesan Client:</Text>
                      <Text style={styles.errorValue}>{latestNetError.errorMessage}</Text>
                    </>
                  )}

                  <Text style={styles.errorLabel}>Response Body dari Server:</Text>
                  <View style={styles.codeContainer}>
                    <ScrollView horizontal nestedScrollEnabled>
                      <Text style={styles.codeText} selectable>
                        {typeof latestNetError.responseData === 'object'
                          ? JSON.stringify(latestNetError.responseData, null, 2)
                          : String(latestNetError.responseData || '(Response kosong / non-JSON)')}
                      </Text>
                    </ScrollView>
                  </View>
                </View>
              ) : (
                <View style={styles.emptyCard}>
                  <Icon name="check-circle-outline" size={36} color="#10b981" />
                  <Text style={styles.emptyCardTitle}>Tidak Ada Error 500 Terdeteksi</Text>
                  <Text style={styles.emptyCardSubtitle}>Semua pemanggilan API berjalan normal.</Text>
                </View>
              )}

              {/* Box JS Crash / Unhandled Error */}
              {latestError && (
                <View style={[styles.errorBox, { borderColor: '#eab308' }]}>
                  <View style={styles.errorBoxHeader}>
                    <Icon name="code-tags" size={20} color="#eab308" />
                    <Text style={[styles.errorBoxTitle, { color: '#eab308' }]}>
                      JavaScript Exception ({latestError.source})
                    </Text>
                  </View>

                  <Text style={styles.errorLabel}>Pesan Error:</Text>
                  <Text style={[styles.errorValue, { color: '#f87171' }]} selectable>
                    {latestError.message}
                  </Text>

                  {latestError.componentStack && (
                    <>
                      <Text style={styles.errorLabel}>Component Stack:</Text>
                      <View style={styles.codeContainer}>
                        <ScrollView horizontal nestedScrollEnabled>
                          <Text style={styles.codeText} selectable>
                            {latestError.componentStack}
                          </Text>
                        </ScrollView>
                      </View>
                    </>
                  )}

                  {latestError.stack && (
                    <>
                      <Text style={styles.errorLabel}>Call Stack:</Text>
                      <View style={styles.codeContainer}>
                        <ScrollView horizontal nestedScrollEnabled>
                          <Text style={styles.codeText} selectable>
                            {latestError.stack}
                          </Text>
                        </ScrollView>
                      </View>
                    </>
                  )}
                </View>
              )}
            </View>
          )}

          {activeTab === 'network' && (
            <View>
              {selectedNetworkItem ? (
                <View style={styles.detailContainer}>
                  <TouchableOpacity
                    style={styles.backBtn}
                    onPress={() => setSelectedNetworkItem(null)}
                  >
                    <Icon name="arrow-left" size={18} color="#38bdf8" />
                    <Text style={styles.backBtnText}>Kembali ke Daftar Request</Text>
                  </TouchableOpacity>

                  <Text style={styles.sectionTitle}>Detail HTTP Request</Text>

                  <Text style={styles.errorLabel}>Method & Status:</Text>
                  <Text style={styles.errorValue}>
                    {selectedNetworkItem.method} — Status:{' '}
                    <Text
                      style={{
                        color: selectedNetworkItem.isError ? '#f43f5e' : '#10b981',
                        fontWeight: 'bold',
                      }}
                    >
                      {selectedNetworkItem.status || 'ERROR'}
                    </Text>{' '}
                    ({selectedNetworkItem.duration}ms)
                  </Text>

                  <Text style={styles.errorLabel}>URL Lengkap:</Text>
                  <Text style={[styles.errorValue, { color: '#38bdf8' }]} selectable>
                    {selectedNetworkItem.fullUrl}
                  </Text>

                  <Text style={styles.errorLabel}>Request Headers:</Text>
                  <View style={styles.codeContainer}>
                    <ScrollView horizontal nestedScrollEnabled>
                      <Text style={styles.codeText} selectable>
                        {JSON.stringify(selectedNetworkItem.requestHeaders || {}, null, 2)}
                      </Text>
                    </ScrollView>
                  </View>

                  {selectedNetworkItem.requestData && (
                    <>
                      <Text style={styles.errorLabel}>Request Payload / Body:</Text>
                      <View style={styles.codeContainer}>
                        <ScrollView horizontal nestedScrollEnabled>
                          <Text style={styles.codeText} selectable>
                            {typeof selectedNetworkItem.requestData === 'object'
                              ? JSON.stringify(selectedNetworkItem.requestData, null, 2)
                              : String(selectedNetworkItem.requestData)}
                          </Text>
                        </ScrollView>
                      </View>
                    </>
                  )}

                  <Text style={styles.errorLabel}>Response Data:</Text>
                  <View style={styles.codeContainer}>
                    <ScrollView horizontal nestedScrollEnabled>
                      <Text style={styles.codeText} selectable>
                        {typeof selectedNetworkItem.responseData === 'object'
                          ? JSON.stringify(selectedNetworkItem.responseData, null, 2)
                          : String(selectedNetworkItem.responseData || '(Kosong)')}
                      </Text>
                    </ScrollView>
                  </View>
                </View>
              ) : (
                <View>
                  <Text style={styles.hintText}>
                    Ketuk pada salah satu baris untuk melihat detail header, request payload, dan response body.
                  </Text>
                  {networkLogs.length === 0 ? (
                    <Text style={styles.emptyText}>Belum ada aktivitas request API.</Text>
                  ) : (
                    networkLogs.map((item) => (
                      <TouchableOpacity
                        key={item.id}
                        style={[
                          styles.networkRow,
                          item.isError && styles.networkRowError,
                        ]}
                        onPress={() => setSelectedNetworkItem(item)}
                      >
                        <View style={styles.networkRowLeft}>
                          <View
                            style={[
                              styles.methodBadge,
                              item.method === 'GET'
                                ? { backgroundColor: '#0284c7' }
                                : item.method === 'POST'
                                ? { backgroundColor: '#059669' }
                                : item.method === 'PUT'
                                ? { backgroundColor: '#d97706' }
                                : { backgroundColor: '#dc2626' },
                            ]}
                          >
                            <Text style={styles.methodBadgeText}>{item.method}</Text>
                          </View>
                          <View style={{ flex: 1, marginLeft: 8 }}>
                            <Text style={styles.networkUrl} numberOfLines={1}>
                              {item.url}
                            </Text>
                            <Text style={styles.networkMeta}>
                              {item.timestamp} • {item.duration}ms
                            </Text>
                          </View>
                        </View>
                        <View
                          style={[
                            styles.statusBadge,
                            {
                              backgroundColor: item.isError ? '#881337' : '#064e3b',
                            },
                          ]}
                        >
                          <Text
                            style={[
                              styles.statusBadgeText,
                              { color: item.isError ? '#f43f5e' : '#34d399' },
                            ]}
                          >
                            {item.status || 'ERR'}
                          </Text>
                        </View>
                      </TouchableOpacity>
                    ))
                  )}
                </View>
              )}
            </View>
          )}

          {activeTab === 'info' && (
            <View style={styles.infoContainer}>
              <Text style={styles.sectionTitle}>Konfigurasi Lingkungan Aplikasi</Text>

              <InfoRow label="Nama Aplikasi" value={Config.APP_NAME} />
              <InfoRow label="Versi Aplikasi" value={Config.APP_VERSION} />
              <InfoRow label="Build Version" value={Config.APP_BUILD} />
              <InfoRow label="Package Name" value={Config.PACKAGE_NAME} />
              <InfoRow label="API Base URL" value={Config.API_URL} />
              <InfoRow label="Platform OS" value={`${Platform.OS} (v${Platform.Version})`} />
              <InfoRow label="Timeout Axios" value={`${Config.AXIOS_TIMEOUT} ms`} />
            </View>
          )}
        </ScrollView>

        {/* Footer Actions */}
        <View style={styles.footer}>
          <TouchableOpacity style={styles.shareBtn} onPress={handleShare}>
            <Icon name="share-variant" size={18} color="#ffffff" />
            <Text style={styles.shareBtnText}>Bagikan Log Error</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.loginBtn} onPress={handleGoToLogin}>
            <Icon name="logout-variant" size={18} color="#ffffff" />
            <Text style={styles.loginBtnText}>Ke Login</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.clearBtn} onPress={handleClear}>
            <Icon name="delete-outline" size={18} color="#94a3b8" />
          </TouchableOpacity>
        </View>
      </View>
    </Modal>
  );
};

const InfoRow = ({ label, value }: { label: string; value: string }) => (
  <View style={styles.infoRow}>
    <Text style={styles.infoLabel}>{label}</Text>
    <Text style={styles.infoValue} selectable>
      {value}
    </Text>
  </View>
);

export const FloatingDebugButton: React.FC<{ onPress: () => void }> = ({ onPress }) => {
  const [errorCount, setErrorCount] = useState<number>(0);

  useEffect(() => {
    const check = () => {
      const netErrors = debugLogger.getNetworkLogs().filter((n) => n.isError).length;
      const jsErrors = debugLogger.getErrorLogs().length;
      setErrorCount(netErrors + jsErrors);
    };

    check();
    const unsub = debugLogger.subscribe(check);
    return () => unsub();
  }, []);

  return (
    <TouchableOpacity style={styles.floatingBtn} onPress={onPress} activeOpacity={0.8}>
      <Icon name="bug" size={20} color="#ffffff" />
      {errorCount > 0 && (
        <View style={styles.badge}>
          <Text style={styles.badgeText}>{errorCount > 9 ? '9+' : errorCount}</Text>
        </View>
      )}
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#0f172a',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    paddingTop: Platform.OS === 'android' ? 24 : 50,
    paddingBottom: 14,
    borderBottomWidth: 1,
    borderBottomColor: '#1e293b',
    backgroundColor: '#0f172a',
  },
  headerLeft: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  headerTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#f8fafc',
  },
  headerSubtitle: {
    fontSize: 11,
    color: '#64748b',
    marginTop: 2,
  },
  closeBtn: {
    padding: 8,
    borderRadius: 8,
    backgroundColor: '#1e293b',
  },
  tabBar: {
    flexDirection: 'row',
    borderBottomWidth: 1,
    borderBottomColor: '#1e293b',
    backgroundColor: '#0f172a',
  },
  tabItem: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 12,
    gap: 6,
    borderBottomWidth: 2,
    borderBottomColor: 'transparent',
  },
  tabItemActive: {
    borderBottomColor: '#38bdf8',
  },
  tabText: {
    fontSize: 12,
    color: '#64748b',
    fontWeight: '500',
  },
  tabTextActive: {
    color: '#f8fafc',
    fontWeight: 'bold',
  },
  content: {
    flex: 1,
    padding: 14,
  },
  errorBox: {
    backgroundColor: '#1e1e2e',
    borderRadius: 10,
    padding: 14,
    borderWidth: 1,
    borderColor: '#e11d48',
    marginBottom: 14,
  },
  errorBoxHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    marginBottom: 10,
    paddingBottom: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#334155',
  },
  errorBoxTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#f43f5e',
  },
  errorLabel: {
    fontSize: 11,
    color: '#94a3b8',
    fontWeight: '600',
    marginTop: 8,
    marginBottom: 2,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  errorValue: {
    fontSize: 12,
    color: '#e2e8f0',
    lineHeight: 18,
  },
  codeContainer: {
    backgroundColor: '#090d16',
    borderRadius: 6,
    padding: 10,
    marginTop: 4,
    borderWidth: 1,
    borderColor: '#1e293b',
    maxHeight: 220,
  },
  codeText: {
    fontFamily: Platform.OS === 'android' ? 'monospace' : 'Courier',
    fontSize: 11,
    color: '#a5f3fc',
    lineHeight: 16,
  },
  emptyCard: {
    backgroundColor: '#1e293b',
    borderRadius: 10,
    padding: 24,
    alignItems: 'center',
    marginBottom: 14,
  },
  emptyCardTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#10b981',
    marginTop: 8,
  },
  emptyCardSubtitle: {
    fontSize: 12,
    color: '#94a3b8',
    marginTop: 4,
  },
  networkRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#1e293b',
    borderRadius: 8,
    padding: 10,
    marginBottom: 8,
    borderLeftWidth: 3,
    borderLeftColor: '#10b981',
  },
  networkRowError: {
    borderLeftColor: '#f43f5e',
    backgroundColor: '#271723',
  },
  networkRowLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  methodBadge: {
    paddingHorizontal: 6,
    paddingVertical: 3,
    borderRadius: 4,
  },
  methodBadgeText: {
    fontSize: 10,
    fontWeight: 'bold',
    color: '#ffffff',
  },
  networkUrl: {
    fontSize: 12,
    color: '#f8fafc',
    fontWeight: '500',
  },
  networkMeta: {
    fontSize: 10,
    color: '#64748b',
    marginTop: 2,
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 6,
  },
  statusBadgeText: {
    fontSize: 11,
    fontWeight: 'bold',
  },
  hintText: {
    fontSize: 11,
    color: '#94a3b8',
    marginBottom: 10,
    fontStyle: 'italic',
  },
  emptyText: {
    fontSize: 12,
    color: '#64748b',
    textAlign: 'center',
    marginTop: 30,
  },
  detailContainer: {
    backgroundColor: '#1e293b',
    borderRadius: 10,
    padding: 14,
  },
  backBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginBottom: 12,
  },
  backBtnText: {
    fontSize: 12,
    color: '#38bdf8',
    fontWeight: '600',
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#f8fafc',
    marginBottom: 10,
  },
  infoContainer: {
    backgroundColor: '#1e293b',
    borderRadius: 10,
    padding: 14,
  },
  infoRow: {
    paddingVertical: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#334155',
  },
  infoLabel: {
    fontSize: 11,
    color: '#94a3b8',
    fontWeight: '600',
  },
  infoValue: {
    fontSize: 12,
    color: '#f8fafc',
    marginTop: 2,
  },
  footer: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    padding: 12,
    paddingBottom: Platform.OS === 'android' ? 16 : 30,
    backgroundColor: '#0f172a',
    borderTopWidth: 1,
    borderTopColor: '#1e293b',
  },
  shareBtn: {
    flex: 1.2,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    backgroundColor: '#0284c7',
    paddingVertical: 12,
    borderRadius: 8,
  },
  shareBtnText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: '#ffffff',
  },
  loginBtn: {
    flex: 0.8,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    backgroundColor: '#dc2626',
    paddingVertical: 12,
    borderRadius: 8,
  },
  loginBtnText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: '#ffffff',
  },
  clearBtn: {
    padding: 12,
    backgroundColor: '#1e293b',
    borderRadius: 8,
  },
  floatingBtn: {
    position: 'absolute',
    bottom: 80,
    right: 16,
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: '#f43f5e',
    alignItems: 'center',
    justifyContent: 'center',
    elevation: 8,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 5,
    zIndex: 9999,
  },
  badge: {
    position: 'absolute',
    top: -2,
    right: -2,
    backgroundColor: '#ffffff',
    borderRadius: 9,
    minWidth: 18,
    height: 18,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 3,
  },
  badgeText: {
    fontSize: 9,
    fontWeight: 'bold',
    color: '#f43f5e',
  },
});

