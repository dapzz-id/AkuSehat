import { execSync } from 'child_process';
import fs from 'fs';
import path from 'path';

const publicDir = path.resolve('public');
const buildDir = path.join(publicDir, 'build');
const zipFile = path.join(publicDir, 'build.zip');

if (!fs.existsSync(buildDir)) {
    console.error('Error: public/build directory does not exist.');
    process.exit(1);
}

try {
    if (fs.existsSync(zipFile)) {
        fs.unlinkSync(zipFile);
    }

    try {
        execSync(`tar -a -cf "${zipFile}" -C "${publicDir}" build`, { stdio: 'inherit' });
    } catch {
        if (process.platform === 'win32') {
            execSync(`powershell -NoProfile -Command "Compress-Archive -Path '${buildDir}' -DestinationPath '${zipFile}' -Force"`, { stdio: 'inherit' });
        } else {
            execSync(`cd "${publicDir}" && zip -r build.zip build`, { stdio: 'inherit' });
        }
    }

    console.log(' Successfully created public/build.zip for hosting deployment.');
} catch (error) {
    console.error('Failed to create public/build.zip:', error.message);
    process.exit(1);
}

