#!/usr/bin/env bash

# ─────────────────────────────────────────────────────────────────────────────
# Frontend Dashboard & Add-ons — WordPress Plugin Build Script
# ─────────────────────────────────────────────────────────────────────────────
# Usage:
#   bash build-plugin.sh                   # Interactive menu (select by number)
#   bash build-plugin.sh 1                 # Build plugin #1 by index
#   bash build-plugin.sh frontend-dashboard# Build specific plugin by slug
#   bash build-plugin.sh all               # Build all plugins in one go
#
# Supported Environments:
#   - Windows (Git Bash / MSYS2 / WSL / PowerShell)
#   - macOS & Linux
# ─────────────────────────────────────────────────────────────────────────────

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Convert POSIX path to Windows path if running under Windows / MSYS / Cygwin / WSL
to_win_path() {
  local p="$1"
  if command -v cygpath >/dev/null 2>&1; then
    cygpath -w "$p"
  elif command -v wslpath >/dev/null 2>&1; then
    wslpath -w "$p"
  else
    echo "$p"
  fi
}

# Determine if a directory is a single WordPress plugin
is_single_plugin_dir() {
  local dir="$1"
  local base_name="$(basename "$dir")"
  if [ -f "$dir/${base_name}.php" ] || [ -f "$dir/plugin.php" ]; then
    return 0
  fi
  return 1
}

# Resolve Plugins Base Directory & Output Directory
if is_single_plugin_dir "$SCRIPT_DIR"; then
  # Running from within a specific plugin folder (e.g. .../plugins/frontend-dashboard/)
  PLUGINS_BASE_DIR="$(dirname "$SCRIPT_DIR")"
  SINGLE_MODE_PLUGIN="$(basename "$SCRIPT_DIR")"
  OUTPUT_DIR="$(dirname "$PLUGINS_BASE_DIR")/dist"
else
  # Running from plugins folder or project root
  if [ -d "$SCRIPT_DIR/wordpress/wp-content/plugins" ]; then
    PLUGINS_BASE_DIR="$SCRIPT_DIR/wordpress/wp-content/plugins"
    OUTPUT_DIR="$SCRIPT_DIR/dist"
  elif [ -d "$SCRIPT_DIR/wp-content/plugins" ]; then
    PLUGINS_BASE_DIR="$SCRIPT_DIR/wp-content/plugins"
    OUTPUT_DIR="$SCRIPT_DIR/dist"
  else
    PLUGINS_BASE_DIR="$SCRIPT_DIR"
    OUTPUT_DIR="$(dirname "$SCRIPT_DIR")/dist"
  fi
  SINGLE_MODE_PLUGIN=""
fi

# Discover FED plugins in base directory
discover_plugins() {
  local search_dir="$1"
  local core_plugin=""
  local addons=()
  
  if [ -d "$search_dir" ]; then
    for d in "$search_dir"/*/; do
      [ -d "$d" ] || continue
      local folder_name="$(basename "$d")"
      
      # Exclude third-party dev plugins or non-plugin folders
      if [ "$folder_name" = "plugin-check" ] || [ "$folder_name" = "dist" ] || [ "$folder_name" = "node_modules" ]; then
        continue
      fi
      
      # Check for main PHP file
      if [ -f "$d/${folder_name}.php" ] || [ -f "$d/plugin.php" ] || [ -f "$d/frontend-dashboard.php" ]; then
        if [ "$folder_name" = "frontend-dashboard" ]; then
          core_plugin="frontend-dashboard"
        else
          addons+=("$folder_name")
        fi
      fi
    done
  fi

  local sorted_addons=($(printf '%s\n' "${addons[@]}" | sort))
  local result=()
  if [ -n "$core_plugin" ]; then
    result+=("$core_plugin")
  fi
  result+=("${sorted_addons[@]}")
  echo "${result[@]}"
}

# Function to build a plugin package
build_plugin() {
  local plugin_slug="$1"
  local plugin_path="${PLUGINS_BASE_DIR}/${plugin_slug}"
  
  if [ ! -d "$plugin_path" ]; then
    echo "❌ Error: Plugin directory not found: $plugin_path"
    return 1
  fi

  mkdir -p "$OUTPUT_DIR"
  local output_zip="${OUTPUT_DIR}/${plugin_slug}.zip"

  echo ""
  echo "╔══════════════════════════════════════════════════════════════════╗"
  printf "║  Building: %-53s ║\n" "$plugin_slug"
  echo "╚══════════════════════════════════════════════════════════════════╝"
  echo "📁 Source : $plugin_path"
  echo "📦 Target : $output_zip"
  echo ""

  # 1. Run build step if package.json contains a build script (e.g. Vite / Tailwind)
  if [ -f "$plugin_path/package.json" ]; then
    if grep -q '"build"' "$plugin_path/package.json" 2>/dev/null; then
      echo "⚡ Compiling frontend assets (npm run build)..."
      (cd "$plugin_path" && npm run build --silent || npm run build)
    fi
  fi

  # 2. Remove old ZIP if present
  if [ -f "$output_zip" ]; then
    echo "🗑️  Removing old ZIP..."
    rm -f "$output_zip"
  fi

  # 3. Create temp staging directory
  echo "📋 Staging production files..."
  local temp_dir=$(mktemp -d 2>/dev/null || mktemp -d -t 'fed_build')
  local stage_dir="${temp_dir}/${plugin_slug}"
  mkdir -p "$stage_dir"

  # Fast copy top-level files/dirs while skipping heavy dev directories
  for item in "$plugin_path"/* "$plugin_path"/.*; do
    [ -e "$item" ] || continue
    local item_name="$(basename "$item")"
    case "$item_name" in
      . | .. | .git | .github | .idea | .vscode | .husky | node_modules | dist | tmp | temp | test* | playwright* | cypress | log)
        continue
        ;;
      *)
        cp -R "$item" "$stage_dir/"
        ;;
    esac
  done

  # 4. Clean any remaining dev-only files
  echo "🧹 Removing dev configurations and artifacts..."
  rm -rf \
    "$stage_dir/.gitignore" \
    "$stage_dir/.gitattributes" \
    "$stage_dir/.editorconfig" \
    "$stage_dir/.eslintrc"* \
    "$stage_dir/.prettier"* \
    "$stage_dir/package.json" \
    "$stage_dir/package-lock.json" \
    "$stage_dir/pnpm-lock.yaml" \
    "$stage_dir/yarn.lock" \
    "$stage_dir/composer.lock" \
    "$stage_dir/vendor/bin" \
    "$stage_dir/vite.config."* \
    "$stage_dir/tailwind.config."* \
    "$stage_dir/postcss.config."* \
    "$stage_dir/tsconfig."* \
    "$stage_dir/eslint.config."* \
    "$stage_dir/playwright.config."* \
    "$stage_dir/cypress.config."* \
    "$stage_dir/phpunit.xml"* \
    "$stage_dir/files.txt" \
    "$stage_dir/build-plugin.sh" \
    "$stage_dir/setup-seeder-assets.sh"

  # Remove OS artifacts and log files
  find "$stage_dir" -name ".DS_Store" -type f -delete 2>/dev/null || true
  find "$stage_dir" -name "Thumbs.db" -type f -delete 2>/dev/null || true
  find "$stage_dir" -name "*.log" -type f -delete 2>/dev/null || true

  # 5. Compress into WordPress-ready ZIP
  echo "🔨 Packaging into WordPress ZIP..."
  
  if command -v powershell.exe >/dev/null 2>&1 || command -v powershell >/dev/null 2>&1; then
    local ps_bin="powershell.exe"
    command -v powershell.exe >/dev/null 2>&1 || ps_bin="powershell"
    local win_stage_dir=$(to_win_path "$stage_dir")
    local win_output_zip=$(to_win_path "$output_zip")
    
    $ps_bin -NoProfile -Command "
      Add-Type -AssemblyName 'System.IO.Compression.FileSystem';
      Add-Type -AssemblyName 'System.IO.Compression';
      \$src = '${win_stage_dir}';
      \$zipPath = '${win_output_zip}';
      if (Test-Path \$zipPath) { Remove-Item \$zipPath -Force };
      \$archive = [System.IO.Compression.ZipFile]::Open(\$zipPath, [System.IO.Compression.ZipArchiveMode]::Create);
      Get-ChildItem -Path \$src -Recurse | ForEach-Object {
        if (-not \$_.PSIsContainer) {
          \$rel = \$_.FullName.Substring(\$src.Length + 1).Replace('\', '/');
          [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(\$archive, \$_.FullName, \$rel, [System.IO.Compression.CompressionLevel]::Optimal) | Out-Null
        }
      };
      \$archive.Dispose();
    "
  elif command -v zip >/dev/null 2>&1; then
    (cd "$stage_dir" && zip -r -q "$output_zip" .)
  else
    echo "❌ Error: Neither powershell.exe nor zip utility found."
    rm -rf "$temp_dir"
    return 1
  fi

  # Cleanup staging
  rm -rf "$temp_dir"

  # 6. Report build summary
  if [ -f "$output_zip" ]; then
    local zip_size=$(du -sh "$output_zip" 2>/dev/null | cut -f1 || ls -lh "$output_zip" | awk '{print $5}')
    echo ""
    echo "✅ Build complete: ${plugin_slug}.zip"
    echo "   📦 File : $output_zip"
    echo "   📏 Size : $zip_size"
    echo "   🚀 Ready to upload: WordPress Admin → Plugins → Add New → Upload Plugin"
    return 0
  else
    echo "❌ Build failed for $plugin_slug. ZIP was not created."
    return 1
  fi
}

# ── Main Entrypoint ──

if [ -n "$SINGLE_MODE_PLUGIN" ]; then
  build_plugin "$SINGLE_MODE_PLUGIN"
  exit 0
fi

PLUGINS=($(discover_plugins "$PLUGINS_BASE_DIR"))

if [ ${#PLUGINS[@]} -eq 0 ]; then
  echo "❌ No WordPress plugins found in: $PLUGINS_BASE_DIR"
  exit 1
fi

ARG="$1"

# Command Line Argument Mode
if [ -n "$ARG" ]; then
  if [ "$ARG" = "all" ] || [ "$ARG" = "ALL" ] || [ "$ARG" = "-a" ] || [ "$ARG" = "--all" ]; then
    echo "🚀 Building ALL ${#PLUGINS[@]} plugins..."
    for p in "${PLUGINS[@]}"; do
      build_plugin "$p"
    done
    echo ""
    echo "🎉 All builds completed successfully! Output: $OUTPUT_DIR"
    exit 0
  elif [[ "$ARG" =~ ^[0-9]+$ ]]; then
    IDX=$((ARG - 1))
    if [ "$IDX" -ge 0 ] && [ "$IDX" -lt "${#PLUGINS[@]}" ]; then
      build_plugin "${PLUGINS[$IDX]}"
      exit 0
    else
      echo "❌ Invalid plugin number: $ARG. Please select between 1 and ${#PLUGINS[@]}."
      exit 1
    fi
  else
    MATCH=""
    for p in "${PLUGINS[@]}"; do
      if [ "$p" = "$ARG" ]; then
        MATCH="$p"
        break
      fi
    done
    if [ -n "$MATCH" ]; then
      build_plugin "$MATCH"
      exit 0
    else
      echo "❌ Plugin '$ARG' not found in $PLUGINS_BASE_DIR."
      exit 1
    fi
  fi
fi

# Interactive Selection Menu
echo ""
echo "╔══════════════════════════════════════════════════════════════════╗"
echo "║             Frontend Dashboard — Plugin Builder Menu             ║"
echo "╚══════════════════════════════════════════════════════════════════╝"
echo ""
echo "Available Plugins in: $PLUGINS_BASE_DIR"
echo ""

i=1
for p in "${PLUGINS[@]}"; do
  if [ "$p" = "frontend-dashboard" ]; then
    printf "  \033[1;32m[%2d]\033[0m %-38s \033[36m(Core Plugin)\033[0m\n" "$i" "$p"
  else
    printf "  \033[1;32m[%2d]\033[0m %-38s \033[90m(Add-on)\033[0m\n" "$i" "$p"
  fi
  i=$((i + 1))
done

echo ""
echo "  [ A] Build ALL Plugins"
echo "  [ Q] Quit"
echo ""
read -r -p "Select a plugin to build [1-${#PLUGINS[@]}, A]: " CHOICE
CHOICE=$(echo "$CHOICE" | tr -d '\r' | xargs)

case "$CHOICE" in
  [aA])
    echo ""
    echo "🚀 Building ALL ${#PLUGINS[@]} plugins..."
    for p in "${PLUGINS[@]}"; do
      build_plugin "$p"
    done
    echo ""
    echo "🎉 All builds completed successfully! Output: $OUTPUT_DIR"
    ;;
  [qQ])
    echo "Operation cancelled."
    exit 0
    ;;
  *)
    if [[ "$CHOICE" =~ ^[0-9]+$ ]]; then
      IDX=$((CHOICE - 1))
      if [ "$IDX" -ge 0 ] && [ "$IDX" -lt "${#PLUGINS[@]}" ]; then
        build_plugin "${PLUGINS[$IDX]}"
      else
        echo "❌ Invalid number selected: $CHOICE"
        exit 1
      fi
    else
      echo "❌ Invalid selection."
      exit 1
    fi
    ;;
esac
