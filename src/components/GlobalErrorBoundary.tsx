import React, { Component, ErrorInfo, ReactNode } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, Platform, ActivityIndicator } from 'react-native';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import debugLogger from '../services/debugLogger';
import { DebugModal } from './DebugModal';
import { navigationRef } from '../navigation/rootNavigation';
import crashReporter from '../services/crashReporter';

interface Props {
  children: ReactNode;
}

interface State {
  hasError: boolean;
  error: Error | null;
  errorInfo: ErrorInfo | null;
  showDebugModal: boolean;
  isReporting: boolean;
  isReportSent: boolean;
  reportFeedback: string | null;
}

export class GlobalErrorBoundary extends Component<Props, State> {
  constructor(props: Props) {
    super(props);
    this.state = {
      hasError: false,
      error: null,
      errorInfo: null,
      showDebugModal: false,
      isReporting: false,
      isReportSent: false,
      reportFeedback: null,
    };
  }

  static getDerivedStateFromError(error: Error): Partial<State> {
    return { hasError: true, error };
  }

  componentDidCatch(error: Error, errorInfo: ErrorInfo) {
    this.setState({ errorInfo });

    // 1. Simpan ke debugLogger lokal
    debugLogger.logError(error, {
      isFatal: true,
      source: 'GlobalErrorBoundary',
      componentStack: errorInfo?.componentStack || undefined,
    });

    // 2. Jika di Production (!__DEV__), kirim otomatis ke Superadmin Crash Analytics di background
    if (!__DEV__) {
      this.sendSilentCrashReport(error, errorInfo?.componentStack || undefined);
    }
  }

  private async sendSilentCrashReport(error: Error, componentStack?: string | null) {
    try {
      this.setState({ isReporting: true });
      const res = await crashReporter.sendCrashReport(error, componentStack);
      if (res.success) {
        this.setState({ isReportSent: true, reportFeedback: 'Laporan crash berhasil dicatat oleh developer.' });
      }
    } catch (e) {
      // Silent fail
    } finally {
      this.setState({ isReporting: false });
    }
  }

  handleManualSendReport = async () => {
    if (this.state.isReportSent) return;
    if (!this.state.error) return;

    this.setState({ isReporting: true, reportFeedback: null });
    const res = await crashReporter.sendCrashReport(
      this.state.error,
      this.state.errorInfo?.componentStack || undefined
    );

    this.setState({
      isReporting: false,
      isReportSent: res.success,
      reportFeedback: res.success
        ? 'Terima kasih! Laporan teknis berhasil terkirim ke tim developer.'
        : 'Gagal mengirim laporan. Pastikan koneksi internet aktif.',
    });
  };

  handleReset = () => {
    this.setState({
      hasError: false,
      error: null,
      errorInfo: null,
      isReportSent: false,
      reportFeedback: null,
    });
    if (navigationRef.isReady()) {
      navigationRef.reset({
        index: 0,
        routes: [{ name: 'Login' }],
      });
    }
  };

  render() {
    if (this.state.hasError) {
      // TAMPILAN KHUSUS DEVELOPMENT (__DEV__)
      if (__DEV__) {
        return (
          <View style={styles.errorContainer}>
            <View style={styles.card}>
              <View style={styles.iconCircleDev}>
                <Icon name="bug" size={36} color="#f43f5e" />
              </View>

              <Text style={styles.title}>Development Error Caught</Text>
              <Text style={styles.subtitle}>
                Terjadi unhandled exception di React tree. Mode Debug aktif untuk inspeksi stack trace.
              </Text>

              <View style={styles.errorBox}>
                <ScrollView style={{ maxHeight: 120 }}>
                  <Text style={styles.errorText} selectable>
                    {this.state.error?.message || 'Unknown Exception'}
                  </Text>
                </ScrollView>
              </View>

              <View style={styles.btnRow}>
                <TouchableOpacity
                  style={styles.debugBtn}
                  onPress={() => this.setState({ showDebugModal: true })}
                >
                  <Icon name="bug" size={18} color="#ffffff" />
                  <Text style={styles.btnText}>Lihat Debug Activity</Text>
                </TouchableOpacity>

                <TouchableOpacity style={styles.resetBtn} onPress={this.handleReset}>
                  <Icon name="restart" size={18} color="#ffffff" />
                  <Text style={styles.btnText}>Kembali ke Login</Text>
                </TouchableOpacity>
              </View>
            </View>

            <DebugModal
              visible={this.state.showDebugModal}
              onClose={() => this.setState({ showDebugModal: false })}
            />
          </View>
        );
      }

      // TAMPILAN KHUSUS PRODUCTION / RELEASE (User-Friendly & Bersih)
      return (
        <View style={styles.errorContainer}>
          <View style={styles.cardProd}>
            <View style={styles.iconCircleProd}>
              <Icon name="alert-circle-outline" size={42} color="#0284c7" />
            </View>

            <Text style={styles.titleProd}>Terjadi Kendala Teknis</Text>
            <Text style={styles.subtitleProd}>
              Mohon maaf atas ketidaknyamanan ini. Aplikasi mendeteksi kendala pada antarmuka dan telah menyiapkan laporan diagnosa otomatis untuk tim pengembang kami.
            </Text>

            {this.state.reportFeedback && (
              <View style={styles.feedbackBanner}>
                <Icon
                  name={this.state.isReportSent ? "check-circle" : "alert-circle"}
                  size={16}
                  color={this.state.isReportSent ? "#10b981" : "#f43f5e"}
                />
                <Text style={styles.feedbackText}>
                  {this.state.reportFeedback}
                </Text>
              </View>
            )}

            <View style={styles.btnRow}>
              {!this.state.isReportSent ? (
                <TouchableOpacity
                  style={[styles.reportBtn, this.state.isReporting && { opacity: 0.7 }]}
                  onPress={this.handleManualSendReport}
                  disabled={this.state.isReporting}
                >
                  {this.state.isReporting ? (
                    <ActivityIndicator size="small" color="#ffffff" />
                  ) : (
                    <Icon name="cloud-upload" size={18} color="#ffffff" />
                  )}
                  <Text style={styles.btnText}>
                    {this.state.isReporting ? 'Mengirim Laporan...' : 'Kirim Laporan ke Developer'}
                  </Text>
                </TouchableOpacity>
              ) : (
                <View style={styles.reportedPill}>
                  <Icon name="check-decagram" size={16} color="#10b981" />
                  <Text style={styles.reportedPillText}>Laporan Terkirim ke Crash Analytics</Text>
                </View>
              )}

              <TouchableOpacity style={styles.resetBtnProd} onPress={this.handleReset}>
                <Icon name="reload" size={18} color="#ffffff" />
                <Text style={styles.btnText}>Muat Ulang / Kembali ke Login</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      );
    }

    return this.props.children;
  }
}

const styles = StyleSheet.create({
  errorContainer: {
    flex: 1,
    backgroundColor: '#090d16',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  card: {
    width: '100%',
    maxWidth: 420,
    backgroundColor: '#1e293b',
    borderRadius: 16,
    padding: 24,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#334155',
    elevation: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.4,
    shadowRadius: 10,
  },
  cardProd: {
    width: '100%',
    maxWidth: 420,
    backgroundColor: '#ffffff',
    borderRadius: 20,
    padding: 24,
    alignItems: 'center',
    elevation: 8,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.15,
    shadowRadius: 12,
  },
  iconCircleDev: {
    width: 64,
    height: 64,
    borderRadius: 32,
    backgroundColor: '#271723',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 16,
    borderWidth: 1,
    borderColor: '#e11d48',
  },
  iconCircleProd: {
    width: 70,
    height: 70,
    borderRadius: 35,
    backgroundColor: '#f0f9ff',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 16,
    borderWidth: 1.5,
    borderColor: '#bae6fd',
  },
  title: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#f8fafc',
    textAlign: 'center',
  },
  titleProd: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#0f172a',
    textAlign: 'center',
  },
  subtitle: {
    fontSize: 13,
    color: '#94a3b8',
    textAlign: 'center',
    marginTop: 8,
    lineHeight: 18,
  },
  subtitleProd: {
    fontSize: 13,
    color: '#64748b',
    textAlign: 'center',
    marginTop: 8,
    lineHeight: 19,
  },
  errorBox: {
    width: '100%',
    backgroundColor: '#0f172a',
    borderRadius: 8,
    padding: 12,
    marginTop: 16,
    borderWidth: 1,
    borderColor: '#e11d48',
  },
  errorText: {
    fontFamily: Platform.OS === 'android' ? 'monospace' : 'Courier',
    fontSize: 11,
    color: '#fca5a5',
    lineHeight: 16,
  },
  feedbackBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: '#f8fafc',
    borderWidth: 1,
    borderColor: '#e2e8f0',
    paddingHorizontal: 12,
    paddingVertical: 10,
    borderRadius: 10,
    marginTop: 16,
    width: '100%',
  },
  feedbackText: {
    fontSize: 12,
    color: '#334155',
    flex: 1,
    lineHeight: 16,
  },
  btnRow: {
    width: '100%',
    gap: 10,
    marginTop: 20,
  },
  debugBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    backgroundColor: '#e11d48',
    paddingVertical: 13,
    borderRadius: 10,
  },
  reportBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    backgroundColor: '#0284c7',
    paddingVertical: 13,
    borderRadius: 12,
  },
  reportedPill: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    backgroundColor: '#ecfdf5',
    borderWidth: 1,
    borderColor: '#a7f3d0',
    paddingVertical: 11,
    borderRadius: 12,
  },
  reportedPillText: {
    fontSize: 12,
    fontWeight: '700',
    color: '#065f46',
  },
  resetBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    backgroundColor: '#0284c7',
    paddingVertical: 13,
    borderRadius: 10,
  },
  resetBtnProd: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    backgroundColor: '#0f172a',
    paddingVertical: 13,
    borderRadius: 12,
  },
  btnText: {
    fontSize: 13,
    fontWeight: 'bold',
    color: '#ffffff',
  },
});

export default GlobalErrorBoundary;
