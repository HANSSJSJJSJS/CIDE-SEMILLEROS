import { execSync } from "child_process";

const comandos = [
  "composer install",
  "npm install",
  "php artisan migrate",
  "php artisan optimize:clear"
];

console.log("🚀 Instalando dependencias...\n");

for (const cmd of comandos) {
  console.log(`➡️ Ejecutando: ${cmd}`);
  try {
    execSync(cmd, { stdio: "inherit" });
  } catch (e) {
    console.error(`❌ Error al ejecutar: ${cmd}`);
  }
}

console.log("\n✅ Instalación completada.");
