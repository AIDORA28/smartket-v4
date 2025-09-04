# Descartar todos los cambios locales y restaurar desde GitHub
git fetch origin
git reset --hard origin/main
git clean -fd