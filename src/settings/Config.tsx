import { Platform } from "react-native"

export const Config = {
    APP_NAME: "Aku Sehat",
    APP_VERSION: "1.0.0",
    APP_BUILD: "Build Stabil",
    PACKAGE_NAME: "neuracakrawira.asia.akusehat",
    DEFAULT_MODERN_THEME_URL: "https://raw.githubusercontent.com/dapzz-id/AkuSehat/theme-atlas-modern/themes/theme_atlas_modern.json",
    API_URL: Platform.OS === "android" ? "http://10.0.2.2:8000/api" : "http://localhost:8000/api",
    AXIOS_TIMEOUT: 15000,
    AXIOS_CREDENTIALS: true,
}