import React, { Component, ErrorInfo, ReactNode } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, Platform } from 'react-native';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import debugLogger from '../services/debugLogger';
import { DebugModal } from './DebugModal';
import { navigationRef } from '../navigation/rootNavigation';

interface Props {
  children: ReactNode;
}

interface State {
  hasError: boolean;
  error: Error | null;
  errorInfo: ErrorInfo | null;
  showDebugModal: boolean;
}

export class GlobalErrorBoundary extends Component<Props, State> {
  constructor(props: Props) {
    super(props);
    this.state = {
      hasError: false,
      error: null,
      errorInfo: null,
      showDebugModal: false,
    };
  }

  static getDerivedStateFromError(error: Error): Partial<State> {
    return { hasError: true, error };
  }

  componentDidCatch(error: Error, errorInfo: ErrorInfo) {
    this.setState({ errorInfo });
    debugLogger.logError(error, {
      isFatal: true,
      source: 'GlobalErrorBoundary',
      componentStack: errorInfo?.componentStack || undefined,
    });
  }

  handleReset = () => {
    this.setState({ hasError: false, error: null, errorInfo: null });
    if (navigationRef.isReady()) {
      navigationRef.reset({
        index: 0,
        routes: [{ name: 'Login' }],
      });
    }
  };

  render() {
    if (this.state.hasError) {
      return (
        <View style={styles.errorContainer}>
          <View style={styles.card}>
            <View style={styles.iconCircle}>
              <Icon name="alert-octagon" size={38} color="#f43f5e" />
            </View>

            <Text style={styles.title}>Terjadi Kendala Tampilan</Text>
            <Text style={styles.subtitle}>
              Aplikasi dicegah menutup paksa (force close). Error sistem telah dicatat pada Debug Activity.
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
  iconCircle: {
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
  title: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#f8fafc',
    textAlign: 'center',
  },
  subtitle: {
    fontSize: 13,
    color: '#94a3b8',
    textAlign: 'center',
    marginTop: 8,
    lineHeight: 18,
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
  resetBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    backgroundColor: '#0284c7',
    paddingVertical: 13,
    borderRadius: 10,
  },
  btnText: {
    fontSize: 13,
    fontWeight: 'bold',
    color: '#ffffff',
  },
});

export default GlobalErrorBoundary;

