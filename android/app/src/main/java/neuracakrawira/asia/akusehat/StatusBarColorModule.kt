package neuracakrawira.asia.akusehat

import android.graphics.Color
import android.os.Build
import androidx.core.view.WindowCompat
import com.facebook.react.bridge.ReactApplicationContext
import com.facebook.react.bridge.ReactContextBaseJavaModule
import com.facebook.react.bridge.ReactMethod

class StatusBarColorModule(reactContext: ReactApplicationContext) : ReactContextBaseJavaModule(reactContext) {
    override fun getName(): String = "StatusBarColorModule"

    @ReactMethod
    fun setColor(colorHex: String, isDark: Boolean) {
        val activity = reactApplicationContext.currentActivity ?: return
        activity.runOnUiThread {
            try {
                val window = activity.window ?: return@runOnUiThread
                val parsedColor = Color.parseColor(colorHex)
                window.statusBarColor = parsedColor
                window.navigationBarColor = parsedColor

                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
                    window.isStatusBarContrastEnforced = false
                    window.isNavigationBarContrastEnforced = false
                }

                val insetsController = WindowCompat.getInsetsController(window, window.decorView)
                // isAppearanceLightStatusBars = true -> dark icons (for light theme)
                // isAppearanceLightStatusBars = false -> white/light icons (for dark theme)
                insetsController.isAppearanceLightStatusBars = !isDark
                insetsController.isAppearanceLightNavigationBars = !isDark
            } catch (e: Exception) {
                // Safe ignore invalid color parse
            }
        }
    }
}
