package neuracakrawira.asia.akusehat

import android.app.Activity
import android.content.Intent
import com.facebook.react.bridge.ActivityEventListener
import com.facebook.react.bridge.Arguments
import com.facebook.react.bridge.BaseActivityEventListener
import com.facebook.react.bridge.Promise
import com.facebook.react.bridge.ReactApplicationContext
import com.facebook.react.bridge.ReactContextBaseJavaModule
import com.facebook.react.bridge.ReactMethod
import com.facebook.react.bridge.WritableMap
import com.google.android.gms.auth.api.signin.GoogleSignIn
import com.google.android.gms.auth.api.signin.GoogleSignInAccount
import com.google.android.gms.auth.api.signin.GoogleSignInClient
import com.google.android.gms.auth.api.signin.GoogleSignInOptions
import com.google.android.gms.common.api.ApiException
import com.google.android.gms.common.api.CommonStatusCodes

class GoogleSignInModule(reactContext: ReactApplicationContext) :
    ReactContextBaseJavaModule(reactContext) {

    private val RC_SIGN_IN = 9001
    private var pendingPromise: Promise? = null
    private var googleSignInClient: GoogleSignInClient? = null

    private val activityEventListener: ActivityEventListener = object : BaseActivityEventListener() {
        override fun onActivityResult(
            activity: Activity,
            requestCode: Int,
            resultCode: Int,
            data: Intent?
        ) {
            if (requestCode == RC_SIGN_IN) {
                val promise = pendingPromise
                pendingPromise = null
                if (promise == null) return

                val task = GoogleSignIn.getSignedInAccountFromIntent(data)
                try {
                    val account: GoogleSignInAccount = task.getResult(ApiException::class.java)
                    val result: WritableMap = Arguments.createMap().apply {
                        putString("idToken", account.idToken)
                        putString("email", account.email)
                        putString("name", account.displayName)
                        putString("id", account.id)
                        putString("photoUrl", account.photoUrl?.toString())
                    }
                    promise.resolve(result)
                } catch (e: ApiException) {
                    val message = when (e.statusCode) {
                        CommonStatusCodes.SIGN_IN_REQUIRED -> "Pengguna membatalkan login Google."
                        CommonStatusCodes.NETWORK_ERROR -> "Koneksi jaringan bermasalah saat menghubungi Google."
                        12501 -> "Login dibatalkan oleh pengguna."
                        12500 -> "Gagal login Google (Error 12500). Pastikan SHA-1 sudah terdaftar di Google Cloud Console."
                        10 -> "Developer Error (Error 10). Pastikan SHA-1 dan Web Client ID cocok di Google Cloud Console."
                        else -> "Gagal login dengan Google (Kode: ${e.statusCode}): ${e.message}"
                    }
                    promise.reject("GOOGLE_SIGN_IN_FAILED", message, e)
                } catch (e: Exception) {
                    promise.reject("GOOGLE_SIGN_IN_EXCEPTION", e.message, e)
                }
            }
        }
    }

    init {
        reactContext.addActivityEventListener(activityEventListener)
    }

    override fun getName(): String = "GoogleSignInModule"

    @ReactMethod
    fun signIn(serverClientId: String, promise: Promise) {
        val activity = reactApplicationContext.currentActivity
        if (activity == null) {
            promise.reject("NO_ACTIVITY", "Activity Android tidak ditemukan.")
            return
        }

        if (pendingPromise != null) {
            promise.reject("IN_PROGRESS", "Proses login Google sedang berjalan.")
            return
        }

        try {
            val gso = GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
                .requestIdToken(serverClientId)
                .requestEmail()
                .requestProfile()
                .build()

            val client = GoogleSignIn.getClient(activity, gso)
            googleSignInClient = client

            // Selalu sign out sesi lokal terlebih dahulu agar dialog pemilihan akun Google selalu muncul
            client.signOut().addOnCompleteListener {
                try {
                    val signInIntent = client.signInIntent
                    pendingPromise = promise
                    activity.startActivityForResult(signInIntent, RC_SIGN_IN)
                } catch (e: Exception) {
                    pendingPromise = null
                    promise.reject("LAUNCH_FAILED", "Gagal membuka jendela Google Sign-In: ${e.message}", e)
                }
            }
        } catch (e: Exception) {
            pendingPromise = null
            promise.reject("GSO_FAILED", "Gagal menginisialisasi Google Sign-In: ${e.message}", e)
        }
    }

    @ReactMethod
    fun signOut(promise: Promise) {
        try {
            val client = googleSignInClient
            if (client != null) {
                client.signOut().addOnCompleteListener {
                    promise.resolve(true)
                }
            } else {
                promise.resolve(true)
            }
        } catch (e: Exception) {
            promise.reject("SIGNOUT_FAILED", e.message, e)
        }
    }
}
