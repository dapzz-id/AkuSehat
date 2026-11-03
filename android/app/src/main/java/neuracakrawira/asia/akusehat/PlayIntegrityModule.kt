package neuracakrawira.asia.akusehat

import android.content.pm.PackageManager
import android.os.Build
import android.util.Log
import com.facebook.react.bridge.Promise
import com.facebook.react.bridge.ReactApplicationContext
import com.facebook.react.bridge.ReactContextBaseJavaModule
import com.facebook.react.bridge.ReactMethod
import com.google.android.play.core.integrity.IntegrityManagerFactory
import com.google.android.play.core.integrity.IntegrityTokenRequest

class PlayIntegrityModule(reactContext: ReactApplicationContext) :
    ReactContextBaseJavaModule(reactContext) {

    override fun getName(): String = "PlayIntegrityModule"

    @ReactMethod
    fun isPlayServicesAvailable(promise: Promise) {
        try {
            val pm = reactApplicationContext.packageManager
            // Cek keberadaan paket Google Play Services ("com.google.android.gms")
            pm.getPackageInfo("com.google.android.gms", 0)
            promise.resolve(true)
        } catch (e: PackageManager.NameNotFoundException) {
            promise.resolve(false)
        } catch (e: Exception) {
            promise.resolve(false)
        }
    }

    /**
     * Memeriksa apakah aplikasi diinstal secara resmi melalui Google Play Store.
     * Tidak memblokir custom ROM / root, hanya mengecek installer package name.
     */
    @ReactMethod
    fun isInstalledFromPlayStore(promise: Promise) {
        try {
            val packageName = reactApplicationContext.packageName
            val pm = reactApplicationContext.packageManager
            val installer = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
                pm.getInstallSourceInfo(packageName).installingPackageName
            } else {
                @Suppress("DEPRECATION")
                pm.getInstallerPackageName(packageName)
            }
            // "com.android.vending" adalah package id resmi dari Google Play Store
            val isPlayStore = installer == "com.android.vending"
            promise.resolve(isPlayStore)
        } catch (e: Exception) {
            promise.resolve(false)
        }
    }

    /**
     * Mendapatkan nama installer package (misal: "com.android.vending" untuk Play Store)
     */
    @ReactMethod
    fun getInstallerPackageName(promise: Promise) {
        try {
            val packageName = reactApplicationContext.packageName
            val pm = reactApplicationContext.packageManager
            val installer = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
                pm.getInstallSourceInfo(packageName).installingPackageName
            } else {
                @Suppress("DEPRECATION")
                pm.getInstallerPackageName(packageName)
            }
            promise.resolve(installer)
        } catch (e: Exception) {
            promise.resolve(null)
        }
    }

    @ReactMethod
    fun requestIntegrityToken(nonce: String, cloudProjectNumberString: String?, promise: Promise) {
        try {
            val integrityManager = IntegrityManagerFactory.create(reactApplicationContext)

            val builder = IntegrityTokenRequest.builder()
                .setNonce(nonce)

            if (!cloudProjectNumberString.isNullOrBlank()) {
                val projectNumber = cloudProjectNumberString.trim().toLongOrNull()
                if (projectNumber != null && projectNumber > 0) {
                    builder.setCloudProjectNumber(projectNumber)
                }
            }

            val request = builder.build()

            integrityManager.requestIntegrityToken(request)
                .addOnSuccessListener { response ->
                    val token = response.token()
                    promise.resolve(token)
                }
                .addOnFailureListener { exception ->
                    Log.w("PlayIntegrity", "Failed to obtain integrity token: ${exception.message}")
                    promise.reject("PLAY_INTEGRITY_FAILED", exception.message, exception)
                }
        } catch (e: Exception) {
            Log.e("PlayIntegrity", "Unexpected error requesting integrity token", e)
            promise.reject("PLAY_INTEGRITY_EXCEPTION", e.message, e)
        }
    }
}
