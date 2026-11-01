package neuracakrawira.asia.akusehat

import android.util.Log
import com.facebook.react.bridge.Promise
import com.facebook.react.bridge.ReactApplicationContext
import com.facebook.react.bridge.ReactContextBaseJavaModule
import com.facebook.react.bridge.ReactMethod
import com.google.android.gms.common.ConnectionResult
import com.google.android.gms.common.GoogleApiAvailability
import com.google.android.play.core.integrity.IntegrityManagerFactory
import com.google.android.play.core.integrity.IntegrityTokenRequest

class PlayIntegrityModule(reactContext: ReactApplicationContext) :
    ReactContextBaseJavaModule(reactContext) {

    override fun getName(): String = "PlayIntegrityModule"

    @ReactMethod
    fun isPlayServicesAvailable(promise: Promise) {
        try {
            val availability = GoogleApiAvailability.getInstance()
            val resultCode = availability.isGooglePlayServicesAvailable(reactApplicationContext)
            promise.resolve(resultCode == ConnectionResult.SUCCESS)
        } catch (e: Exception) {
            promise.resolve(false)
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
