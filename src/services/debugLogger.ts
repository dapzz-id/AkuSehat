/**
 * DebugLogger Service
 * Menangkap seluruh aktivitas jaringan (API), error unhandled, dan status code (misal 500)
 * untuk ditampilkan pada Debug Activity / Modal agar tidak force close.
 */

export interface NetworkLogItem {
  id: string;
  timestamp: string;
  method: string;
  url: string;
  fullUrl: string;
  status?: number;
  duration: number;
  requestHeaders?: Record<string, any>;
  requestData?: any;
  responseHeaders?: Record<string, any>;
  responseData?: any;
  errorMessage?: string;
  isError: boolean;
}

export interface ErrorLogItem {
  id: string;
  timestamp: string;
  message: string;
  stack?: string;
  componentStack?: string;
  isFatal?: boolean;
  source: string;
  extra?: any;
}

type DebugListener = () => void;
type ErrorTriggerListener = (error: ErrorLogItem | NetworkLogItem) => void;

class DebugLoggerService {
  private networkLogs: NetworkLogItem[] = [];
  private errorLogs: ErrorLogItem[] = [];
  private listeners: Set<DebugListener> = new Set();
  private autoOpenListeners: Set<ErrorTriggerListener> = new Set();
  private maxLogs: number = 40;

  constructor() {
    this.setupGlobalHandlers();
  }

  private setupGlobalHandlers() {
    // 1. Tangkap unhandled JavaScript error di React Native
    try {
      const g = typeof globalThis !== 'undefined' ? (globalThis as any) : null;
      const globalErrorUtils = g?.ErrorUtils;
      if (globalErrorUtils && typeof globalErrorUtils.getGlobalHandler === 'function') {
        const defaultHandler = globalErrorUtils.getGlobalHandler();
        globalErrorUtils.setGlobalHandler((error: any, isFatal?: boolean) => {
          this.logError(error, { isFatal: !!isFatal, source: 'GlobalErrorUtils' });
          // Jangan biarkan Android menutup aplikasi secara fatal jika bisa ditangani
          if (!isFatal && defaultHandler) {
            defaultHandler(error, isFatal);
          }
        });
      }
    } catch (e) {
      console.warn('[DebugLogger] Gagal memasang GlobalErrorUtils handler:', e);
    }

    // 2. Tangkap Unhandled Promise Rejections
    try {
      const g = typeof globalThis !== 'undefined' ? (globalThis as any) : null;
      if (g) {
        const originalRejectionHandler = g.onunhandledrejection;
        g.onunhandledrejection = (event: any) => {
          const reason = event?.reason || event;
          this.logError(reason, { source: 'UnhandledPromiseRejection' });
          if (originalRejectionHandler) {
            originalRejectionHandler(event);
          }
        };
      }
    } catch (e) {
      console.warn('[DebugLogger] Gagal memasang unhandledrejection handler:', e);
    }
  }

  public subscribe(listener: DebugListener): () => void {
    this.listeners.add(listener);
    return () => this.listeners.delete(listener);
  }

  public onAutoOpenTrigger(listener: ErrorTriggerListener): () => void {
    this.autoOpenListeners.add(listener);
    return () => this.autoOpenListeners.delete(listener);
  }

  private notify() {
    this.listeners.forEach((listener) => {
      try {
        listener();
      } catch (err) {
        console.warn('[DebugLogger] Listener error:', err);
      }
    });
  }

  private triggerAutoOpen(item: ErrorLogItem | NetworkLogItem) {
    this.autoOpenListeners.forEach((listener) => {
      try {
        listener(item);
      } catch (err) {
        console.warn('[DebugLogger] AutoOpen listener error:', err);
      }
    });
  }

  public logNetwork(
    config: any,
    response?: any,
    error?: any,
    startTime?: number
  ): NetworkLogItem {
    const duration = startTime ? Date.now() - startTime : 0;
    const now = new Date();
    const timeStr = now.toTimeString().split(' ')[0] + '.' + String(now.getMilliseconds()).padStart(3, '0');

    const method = (config?.method || 'GET').toUpperCase();
    const url = config?.url || '';
    const baseURL = config?.baseURL || '';
    const fullUrl = url.startsWith('http') ? url : `${baseURL.replace(/\/+$/, '')}/${url.replace(/^\/+/, '')}`;

    const status = response?.status || error?.response?.status;
    const isError = !status || status >= 400 || !!error;

    let responseData = response?.data || error?.response?.data;
    // Sanitasi data jika berupa string html error dari laravel
    if (typeof responseData === 'string' && responseData.length > 5000) {
      responseData = responseData.substring(0, 5000) + '... (terpotong)';
    }

    const item: NetworkLogItem = {
      id: `${Date.now()}-${Math.random().toString(36).substring(2, 7)}`,
      timestamp: timeStr,
      method,
      url,
      fullUrl,
      status,
      duration,
      requestHeaders: config?.headers,
      requestData: config?.data,
      responseHeaders: response?.headers || error?.response?.headers,
      responseData,
      errorMessage: error?.message,
      isError,
    };

    this.networkLogs.unshift(item);
    if (this.networkLogs.length > this.maxLogs) {
      this.networkLogs.pop();
    }

    this.notify();

    // Jika terjadi error 500, otomatis buka Debug Activity
    if (status >= 500) {
      this.triggerAutoOpen(item);
    }

    return item;
  }

  public logError(
    err: any,
    meta: { isFatal?: boolean; source?: string; componentStack?: string; extra?: any } = {}
  ): ErrorLogItem {
    const now = new Date();
    const timeStr = now.toTimeString().split(' ')[0] + '.' + String(now.getMilliseconds()).padStart(3, '0');

    const message = err?.message || (typeof err === 'string' ? err : 'Unknown JavaScript Error');
    const stack = err?.stack || '';

    const item: ErrorLogItem = {
      id: `${Date.now()}-${Math.random().toString(36).substring(2, 7)}`,
      timestamp: timeStr,
      message,
      stack,
      componentStack: meta.componentStack,
      isFatal: meta.isFatal,
      source: meta.source || 'General',
      extra: meta.extra,
    };

    this.errorLogs.unshift(item);
    if (this.errorLogs.length > this.maxLogs) {
      this.errorLogs.pop();
    }

    this.notify();
    this.triggerAutoOpen(item);

    return item;
  }

  public getNetworkLogs(): NetworkLogItem[] {
    return [...this.networkLogs];
  }

  public getErrorLogs(): ErrorLogItem[] {
    return [...this.errorLogs];
  }

  public getLatestError(): ErrorLogItem | undefined {
    return this.errorLogs[0];
  }

  public getLatestNetworkError(): NetworkLogItem | undefined {
    return this.networkLogs.find((n) => n.isError);
  }

  public clearAll() {
    this.networkLogs = [];
    this.errorLogs = [];
    this.notify();
  }

  public getDiagnosticSummary(): string {
    const errorCount = this.errorLogs.length;
    const networkCount = this.networkLogs.length;
    const latestError = this.errorLogs[0];
    const latestNetError = this.networkLogs.find((n) => n.isError);

    let report = `=== LAPORAN DIAGNOSTIK DEBUG AKU SEHAT ===\n`;
    report += `Waktu: ${new Date().toISOString()}\n`;
    report += `Total Error: ${errorCount} | Total Request: ${networkCount}\n\n`;

    if (latestNetError) {
      report += `--- REQUESR ERROR TERAKHIR (${latestNetError.status || 'UNKNOWN'}) ---\n`;
      report += `Method: ${latestNetError.method}\n`;
      report += `URL: ${latestNetError.fullUrl}\n`;
      report += `Status Code: ${latestNetError.status}\n`;
      report += `Pesan: ${latestNetError.errorMessage || '-'}\n`;
      report += `Response Body:\n${typeof latestNetError.responseData === 'object' ? JSON.stringify(latestNetError.responseData, null, 2) : String(latestNetError.responseData)}\n\n`;
    }

    if (latestError) {
      report += `--- JS ERROR / CRASH TERAKHIR ---\n`;
      report += `Sumber: ${latestError.source} (Fatal: ${latestError.isFatal ? 'Ya' : 'Tidak'})\n`;
      report += `Pesan: ${latestError.message}\n`;
      if (latestError.componentStack) {
        report += `Component Stack:\n${latestError.componentStack}\n`;
      }
      if (latestError.stack) {
        report += `Call Stack:\n${latestError.stack}\n`;
      }
      report += `\n`;
    }

    report += `--- RIWAYAT 5 REQUEST TERAKHIR ---\n`;
    this.networkLogs.slice(0, 5).forEach((n, idx) => {
      report += `${idx + 1}. [${n.timestamp}] ${n.method} ${n.url} => ${n.status || 'ERR'} (${n.duration}ms)\n`;
      if (n.isError && n.responseData) {
        report += `   Resp: ${typeof n.responseData === 'object' ? JSON.stringify(n.responseData) : String(n.responseData).substring(0, 150)}\n`;
      }
    });

    return report;
  }
}

export const debugLogger = new DebugLoggerService();
export default debugLogger;
