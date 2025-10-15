<?php
/**
 * Test Windshield Signs Page
 * עמוד בדיקה לשלטי שמשונית
 */

// Simple test without session requirements
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>בדיקת שלטי שמשונית</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bi bi-grid-3x3 me-2"></i>
                            בדיקת שלטי שמשונית
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="testForm">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="recentSearches" class="form-label">חיפושים אחרונים</label>
                                    <select class="form-select" id="recentSearches" onchange="if(this.value !== '') loadFromRecentSearch(this.value)">
                                        <option value="">חיפושים אחרונים...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="width" class="form-label">רוחב השלט (ס"מ)</label>
                                    <input type="number" class="form-control" id="width" value="200" min="1" max="1000" step="0.1">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="height" class="form-label">גובה השלט (ס"מ)</label>
                                    <input type="number" class="form-control" id="height" value="100" min="1" max="1000" step="0.1">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edge_distance" class="form-label">מרחק מקצה (ס"מ)</label>
                                    <input type="number" class="form-control" id="edge_distance" value="2" min="0.1" max="10" step="0.1">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hole_diameter" class="form-label">קוטר החור (ס"מ)</label>
                                    <input type="number" class="form-control" id="hole_diameter" value="0.8" min="0.1" max="5" step="0.1">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="rings_width" class="form-label">כמות טבעות לרוחב</label>
                                    <input type="number" class="form-control" id="rings_width" value="5" min="2" max="20">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="rings_height" class="form-label">כמות טבעות לגובה</label>
                                    <input type="number" class="form-control" id="rings_height" value="3" min="2" max="20">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="spacing_x" class="form-label">מרווח בין טבעות לרוחב (ס"מ)</label>
                                    <input type="number" class="form-control" id="spacing_x" value="49.0" min="0" max="200" step="0.1">
                                    <small class="form-text text-muted">0 = מרווח אוטומטי</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="spacing_y" class="form-label">מרווח בין טבעות לגובה (ס"מ)</label>
                                    <input type="number" class="form-control" id="spacing_y" value="48.0" min="0" max="200" step="0.1">
                                    <small class="form-text text-muted">0 = מרווח אוטומטי</small>
                                </div>

                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetHolesOnly()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>
                                    איפוס חורים לסטנדרט
                                </button>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-primary" onclick="downloadSVGFile()">
                                        <i class="bi bi-download me-2"></i>
                                        הורד SVG
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div id="preview" class="mt-4" style="display: block;">
                            <h6>תצוגה מקדימה:</h6>
                            <div id="previewContent" class="border p-3 text-center bg-light">
                                <p class="text-muted">טוען תצוגה מקדימה...</p>
                            </div>
                        </div>


                        <div id="result" class="mt-4" style="display: none;">
                            <h6>תוצאה:</h6>
                            <div id="resultContent" class="alert alert-info"></div>

                            <!-- Download Buttons -->
                            <div id="downloadButtons" class="mt-3" style="display: none;">
                                <button type="button" class="btn btn-primary me-2" id="downloadSvgBtn">
                                    <i class="bi bi-download me-2"></i>
                                    הורד SVG
                                </button>
                                <button type="button" class="btn btn-success me-2" id="downloadPdfBtn">
                                    <i class="bi bi-download me-2"></i>
                                    הורד PDF
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="copySvgBtn">
                                    <i class="bi bi-clipboard me-2"></i>
                                    העתק SVG
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Local storage functions
        function saveToLocalStorage() {
            const data = {
                width: document.getElementById('width').value,
                height: document.getElementById('height').value,
                edge_distance: document.getElementById('edge_distance').value,
                hole_diameter: document.getElementById('hole_diameter').value,
                rings_width: document.getElementById('rings_width').value,
                rings_height: document.getElementById('rings_height').value,
                spacing_x: document.getElementById('spacing_x').value,
                spacing_y: document.getElementById('spacing_y').value
            };
            localStorage.setItem('windshield_signs_data', JSON.stringify(data));
        }

        function loadFromLocalStorage() {
            // Don't load from localStorage on page load - always use defaults
            // localStorage is only used for saving current session data
        }

        function loadDefaultValues() {
            // Set default values
            document.getElementById('width').value = '200';
            document.getElementById('height').value = '100';
            document.getElementById('edge_distance').value = '2';
            document.getElementById('hole_diameter').value = '0.8';

            // Calculate rings count to get closest to 50cm spacing
            const width = 200 * 10; // Convert to mm
            const height = 100 * 10; // Convert to mm
            const edgeDistance = 2 * 10; // Convert to mm
            const targetSpacing = 50 * 10; // 50cm in mm

            const topBottomLength = width - 2 * edgeDistance; // 1960mm
            const leftRightLength = height - 2 * edgeDistance; // 960mm

            // Calculate rings to get closest to 50cm spacing
            const ringsWidth = Math.round(topBottomLength / targetSpacing) + 1; // 1960/500 + 1 = 5
            const ringsHeight = Math.round(leftRightLength / targetSpacing) + 1; // 960/500 + 1 = 3

            document.getElementById('rings_width').value = ringsWidth;
            document.getElementById('rings_height').value = ringsHeight;

            // Calculate actual spacing
            const actualSpacingX = topBottomLength / (ringsWidth - 1);
            const actualSpacingY = leftRightLength / (ringsHeight - 1);

            document.getElementById('spacing_x').value = (actualSpacingX / 10).toFixed(1);
            document.getElementById('spacing_y').value = (actualSpacingY / 10).toFixed(1);
        }

        function loadRecentSearches() {
            const recent = localStorage.getItem('windshield_recent_searches');
            if (recent) {
                const searches = JSON.parse(recent);
                const dropdown = document.getElementById('recentSearches');
                dropdown.innerHTML = '<option value="">חיפושים אחרונים...</option>';

                searches.forEach((search, index) => {
                    const option = document.createElement('option');
                    option.value = index;
                    option.textContent = `${search.width}×${search.height} - ${search.rings_width}×${search.rings_height} טבעות`;
                    dropdown.appendChild(option);
                });
            }
        }

        function saveRecentSearch() {
            const data = {
                width: document.getElementById('width').value,
                height: document.getElementById('height').value,
                edge_distance: document.getElementById('edge_distance').value,
                hole_diameter: document.getElementById('hole_diameter').value,
                rings_width: document.getElementById('rings_width').value,
                rings_height: document.getElementById('rings_height').value,
                spacing_x: document.getElementById('spacing_x').value,
                spacing_y: document.getElementById('spacing_y').value,
                timestamp: new Date().toLocaleString('he-IL')
            };

            let recent = JSON.parse(localStorage.getItem('windshield_recent_searches') || '[]');

            // Remove duplicate if exists
            recent = recent.filter(item =>
                !(item.width === data.width && item.height === data.height &&
                  item.rings_width === data.rings_width && item.rings_height === data.rings_height)
            );

            // Add to beginning
            recent.unshift(data);

            // Keep only last 5
            recent = recent.slice(0, 5);

            localStorage.setItem('windshield_recent_searches', JSON.stringify(recent));
            loadRecentSearches();
        }

        function loadFromRecentSearch(index) {
            const recent = JSON.parse(localStorage.getItem('windshield_recent_searches') || '[]');
            if (recent[index]) {
                const data = recent[index];
                document.getElementById('width').value = data.width;
                document.getElementById('height').value = data.height;
                document.getElementById('edge_distance').value = data.edge_distance;
                document.getElementById('hole_diameter').value = data.hole_diameter;
                document.getElementById('rings_width').value = data.rings_width;
                document.getElementById('rings_height').value = data.rings_height;
                document.getElementById('spacing_x').value = data.spacing_x;
                document.getElementById('spacing_y').value = data.spacing_y;

                // Update spacing after loading
                updateSpacingFromCount();
                // Update preview
                updatePreviewAndCode();
            }
        }

        function clearLocalStorage() {
            localStorage.removeItem('windshield_signs_data');
            location.reload(); // Reload to reset to defaults
        }

        function resetHolesOnly() {
            // Reset only hole-related parameters, keep dimensions
            const targetSpacing = 50; // cm

            // Calculate optimal rings for current dimensions
            const currentWidth = parseFloat(document.getElementById('width').value);
            const currentHeight = parseFloat(document.getElementById('height').value);
            const edgeDistance = 2; // cm

            // Calculate rings to get closest to 50cm spacing
            const ringsWidth = Math.max(2, Math.round((currentWidth - 2 * edgeDistance) / targetSpacing) + 1);
            const ringsHeight = Math.max(2, Math.round((currentHeight - 2 * edgeDistance) / targetSpacing) + 1);

            // Calculate actual spacing
            const actualSpacingX = (currentWidth - 2 * edgeDistance) / (ringsWidth - 1);
            const actualSpacingY = (currentHeight - 2 * edgeDistance) / (ringsHeight - 1);

            // Set hole-related values
            document.getElementById('edge_distance').value = edgeDistance;
            document.getElementById('hole_diameter').value = 0.8;
            document.getElementById('rings_width').value = ringsWidth;
            document.getElementById('rings_height').value = ringsHeight;
            document.getElementById('spacing_x').value = actualSpacingX.toFixed(1);
            document.getElementById('spacing_y').value = actualSpacingY.toFixed(1);

            // Update preview
            updatePreviewAndCode();
        }

        function clearRecentSearches() {
            localStorage.removeItem('windshield_recent_searches');
            loadRecentSearches();
        }

        // Auto-update spacing when rings count changes
        document.addEventListener('DOMContentLoaded', function() {
            // Load default values (not from localStorage)
            loadDefaultValues();
            // Load recent searches
            loadRecentSearches();
            // Show initial preview
            setTimeout(() => {
                testPreview();
            }, 200);
            // Listen to rings count changes
            document.getElementById('rings_width').addEventListener('change', function() {
                updateSpacingFromCount();
                updatePreviewAndCode();
                saveToLocalStorage();
            });
            document.getElementById('rings_height').addEventListener('change', function() {
                updateSpacingFromCount();
                updatePreviewAndCode();
                saveToLocalStorage();
            });

            // Listen to dimension changes
            ['width', 'height', 'edge_distance'].forEach(id => {
                document.getElementById(id).addEventListener('change', function() {
                    updateRingsFromDimensions(); // Update rings to maintain ~50cm spacing
                    updatePreviewAndCode();
                    saveToLocalStorage();
                });
            });

            // Listen to hole diameter changes (doesn't affect rings count)
            document.getElementById('hole_diameter').addEventListener('input', function() {
                updatePreviewAndCode();
                saveToLocalStorage();
            });

            // Listen to spacing changes
            document.getElementById('spacing_x').addEventListener('change', function() {
                updateCountFromSpacing();
                updatePreviewAndCode();
                saveToLocalStorage();
            });
            document.getElementById('spacing_y').addEventListener('change', function() {
                updateCountFromSpacing();
                updatePreviewAndCode();
                saveToLocalStorage();
            });
        });

        function updateSpacingFromCount() {
            const width = parseFloat(document.getElementById('width').value) * 10; // Convert cm to mm
            const height = parseFloat(document.getElementById('height').value) * 10; // Convert cm to mm
            const edgeDistance = parseFloat(document.getElementById('edge_distance').value) * 10; // Convert cm to mm
            const ringsWidth = parseInt(document.getElementById('rings_width').value);
            const ringsHeight = parseInt(document.getElementById('rings_height').value);

            // Calculate spacing for each direction
            const topBottomLength = width - 2 * edgeDistance;
            const leftRightLength = height - 2 * edgeDistance;

            // Spacing between rings (not including corners)
            const spacingX = ringsWidth > 1 ? topBottomLength / (ringsWidth - 1) : 0;
            const spacingY = ringsHeight > 1 ? leftRightLength / (ringsHeight - 1) : 0;

            // Update the spacing fields (convert back to cm)
            document.getElementById('spacing_x').value = (spacingX / 10).toFixed(1);
            document.getElementById('spacing_y').value = (spacingY / 10).toFixed(1);
        }

        function updateRingsFromDimensions() {
            const width = parseFloat(document.getElementById('width').value) * 10; // Convert cm to mm
            const height = parseFloat(document.getElementById('height').value) * 10; // Convert cm to mm
            const edgeDistance = parseFloat(document.getElementById('edge_distance').value) * 10; // Convert cm to mm
            const targetSpacing = 50 * 10; // 50cm in mm

            const topBottomLength = width - 2 * edgeDistance;
            const leftRightLength = height - 2 * edgeDistance;

            // Calculate rings to get closest to 50cm spacing
            const ringsWidth = Math.max(2, Math.round(topBottomLength / targetSpacing) + 1);
            const ringsHeight = Math.max(2, Math.round(leftRightLength / targetSpacing) + 1);

            // Update the rings count fields
            document.getElementById('rings_width').value = ringsWidth;
            document.getElementById('rings_height').value = ringsHeight;

            // Update spacing to match
            updateSpacingFromCount();
        }

        function updatePreviewAndCode() {
            // Update preview automatically
            testPreview();
        }

        function updateCountFromSpacing() {
            const width = parseFloat(document.getElementById('width').value) * 10; // Convert cm to mm
            const height = parseFloat(document.getElementById('height').value) * 10; // Convert cm to mm
            const edgeDistance = parseFloat(document.getElementById('edge_distance').value) * 10; // Convert cm to mm
            const spacingX = parseFloat(document.getElementById('spacing_x').value) * 10; // Convert cm to mm
            const spacingY = parseFloat(document.getElementById('spacing_y').value) * 10; // Convert cm to mm

            // Calculate how many rings fit
            const topBottomLength = width - 2 * edgeDistance;
            const leftRightLength = height - 2 * edgeDistance;

            // If spacing is 0, don't change the count
            if (spacingX === 0 || spacingY === 0) return;

            // Calculate rings: for spacing X, we need to see how many rings fit
            // If we have 2 rings, spacing is the full length
            // If we have 3 rings, we have 2 spaces of the given spacing
            // So: rings = (length / spacing) + 1
            const ringsWidth = Math.max(2, Math.round(topBottomLength / spacingX) + 1);
            const ringsHeight = Math.max(2, Math.round(leftRightLength / spacingY) + 1);

            // Update the rings count fields
            document.getElementById('rings_width').value = ringsWidth;
            document.getElementById('rings_height').value = ringsHeight;
        }

        function showSVGCode() {
            try {
                console.log('showSVGCode called');

                // Get values
                const width = parseFloat(document.getElementById('width').value) * 10;
                const height = parseFloat(document.getElementById('height').value) * 10;
                const edgeDistance = parseFloat(document.getElementById('edge_distance').value) * 10;
                const holeDiameter = parseFloat(document.getElementById('hole_diameter').value) * 10;
                const ringsWidth = parseInt(document.getElementById('rings_width').value);
                const ringsHeight = parseInt(document.getElementById('rings_height').value);
                const spacingX = parseFloat(document.getElementById('spacing_x').value) * 10;
                const spacingY = parseFloat(document.getElementById('spacing_y').value) * 10;
                const radius = holeDiameter / 2;

                console.log('Values:', { width, height, edgeDistance, holeDiameter, ringsWidth, ringsHeight, spacingX, spacingY, radius });

                // Calculate spacing
                const topBottomLength = width - 2 * edgeDistance;
                const leftRightLength = height - 2 * edgeDistance;
                const actualSpacingX = spacingX === 0 ? topBottomLength / (ringsWidth - 1) : spacingX;
                const actualSpacingY = spacingY === 0 ? leftRightLength / (ringsHeight - 1) : spacingY;

                console.log('Spacing calculated:', { actualSpacingX, actualSpacingY });

                // Generate rings
                const rings = [];

                // Top edge
                for (let i = 0; i < ringsWidth; i++) {
                    rings.push({
                        x: edgeDistance + (i * actualSpacingX),
                        y: edgeDistance
                    });
                }

                // Right edge (excluding top corner)
                for (let i = 1; i < ringsHeight; i++) {
                    rings.push({
                        x: width - edgeDistance,
                        y: edgeDistance + (i * actualSpacingY)
                    });
                }

                // Bottom edge (excluding right corner)
                for (let i = ringsWidth - 2; i >= 0; i--) {
                    rings.push({
                        x: edgeDistance + (i * actualSpacingX),
                        y: height - edgeDistance
                    });
                }

                // Left edge (excluding bottom and top corners)
                for (let i = ringsHeight - 2; i > 0; i--) {
                    rings.push({
                        x: edgeDistance,
                        y: edgeDistance + (i * actualSpacingY)
                    });
                }

                console.log('Rings calculated:', rings.length);

                // Generate SVG
                let circlesHTML = '';
                rings.forEach(ring => {
                    circlesHTML += `<circle cx="${ring.x}" cy="${ring.y}" r="${radius}" fill="none" stroke="red" stroke-width="0.7"/>`;
                });

                const svgContent = `<?xml version="1.0" encoding="UTF-8"?>
<svg width="${width}mm" height="${height}mm" viewBox="0 0 ${width} ${height}" xmlns="http://www.w3.org/2000/svg">
    <rect x="0" y="0" width="${width}" height="${height}" fill="none" stroke="black" stroke-width="0.7"/>
    ${circlesHTML}
</svg>`;

                console.log('SVG generated, length:', svgContent.length);

                // Show the code
                document.getElementById('svgCode').style.display = 'block';
                document.getElementById('svgCodeContent').textContent = svgContent;

                console.log('SVG code displayed');

            } catch (error) {
                console.error('Error in showSVGCode:', error);
                alert('שגיאה ביצירת קוד SVG: ' + error.message);
            }
        }

        function downloadSVGFile() {
            console.log('downloadSVGFile called');

            // Generate SVG content directly (same as showSVGCode)
            const width = parseFloat(document.getElementById('width').value) * 10;
            const height = parseFloat(document.getElementById('height').value) * 10;
            const edgeDistance = parseFloat(document.getElementById('edge_distance').value) * 10;
            const holeDiameter = parseFloat(document.getElementById('hole_diameter').value) * 10;
            const ringsWidth = parseInt(document.getElementById('rings_width').value);
            const ringsHeight = parseInt(document.getElementById('rings_height').value);
            const spacingX = parseFloat(document.getElementById('spacing_x').value) * 10;
            const spacingY = parseFloat(document.getElementById('spacing_y').value) * 10;
            const radius = holeDiameter / 2;

            // Calculate spacing
            const topBottomLength = width - 2 * edgeDistance;
            const leftRightLength = height - 2 * edgeDistance;
            const actualSpacingX = spacingX === 0 ? topBottomLength / (ringsWidth - 1) : spacingX;
            const actualSpacingY = spacingY === 0 ? leftRightLength / (ringsHeight - 1) : spacingY;

            // Generate rings
            const rings = [];

            // Top edge
            for (let i = 0; i < ringsWidth; i++) {
                rings.push({
                    x: edgeDistance + (i * actualSpacingX),
                    y: edgeDistance
                });
            }

            // Right edge (excluding top corner)
            for (let i = 1; i < ringsHeight; i++) {
                rings.push({
                    x: width - edgeDistance,
                    y: edgeDistance + (i * actualSpacingY)
                });
            }

            // Bottom edge (excluding right corner)
            for (let i = ringsWidth - 2; i >= 0; i--) {
                rings.push({
                    x: edgeDistance + (i * actualSpacingX),
                    y: height - edgeDistance
                });
            }

            // Left edge (excluding bottom and top corners)
            for (let i = ringsHeight - 2; i > 0; i--) {
                rings.push({
                    x: edgeDistance,
                    y: edgeDistance + (i * actualSpacingY)
                });
            }

            // Generate SVG
            let circlesHTML = '';
            rings.forEach(ring => {
                circlesHTML += `<circle cx="${ring.x}" cy="${ring.y}" r="${radius}" fill="none" stroke="red" stroke-width="0.7"/>`;
            });

            const svgContent = `<?xml version="1.0" encoding="UTF-8"?>
<svg width="${width}mm" height="${height}mm" viewBox="0 0 ${width} ${height}" xmlns="http://www.w3.org/2000/svg">
    <rect x="0" y="0" width="${width}" height="${height}" fill="none" stroke="black" stroke-width="0.7"/>
    ${circlesHTML}
</svg>`;

            console.log('SVG content generated, length:', svgContent.length);

            // Get current values for filename
            const widthValue = document.getElementById('width').value;
            const heightValue = document.getElementById('height').value;
            const ringsWidthValue = document.getElementById('rings_width').value;
            const ringsHeightValue = document.getElementById('rings_height').value;

            // Create filename
            const filename = `windshield_sign_${widthValue}x${heightValue}_${ringsWidthValue}x${ringsHeightValue}_rings.svg`;
            console.log('Creating file:', filename);

            // Create blob with SVG content
            const blob = new Blob([svgContent], {
                type: 'image/svg+xml'
            });
            console.log('Blob created, size:', blob.size);

            // Create download URL
            const url = URL.createObjectURL(blob);
            console.log('Download URL created');

            // Create download link
            const downloadLink = document.createElement('a');
            downloadLink.href = url;
            downloadLink.download = filename;
            downloadLink.style.display = 'none';

            // Add to page and trigger download
            document.body.appendChild(downloadLink);
            downloadLink.click();

            // Clean up
            document.body.removeChild(downloadLink);
            URL.revokeObjectURL(url);

            console.log('Download completed successfully');

            // Save as recent search
            saveRecentSearch();
        }


        // Convert units to mm
        function convertToMM(value, unit) {
            const conversions = {
                'mm': 1,
                'cm': 10,
                'm': 1000
            };
            return parseFloat(value) * (conversions[unit] || 1);
        }

        function testPreview() {
            // Save as recent search
            saveRecentSearch();

            const width = parseFloat(document.getElementById('width').value) * 10; // Convert cm to mm
            const height = parseFloat(document.getElementById('height').value) * 10; // Convert cm to mm
            const edgeDistance = parseFloat(document.getElementById('edge_distance').value) * 10; // Convert cm to mm
            const holeDiameter = parseFloat(document.getElementById('hole_diameter').value) * 10; // Convert cm to mm
            const ringsWidth = parseInt(document.getElementById('rings_width').value);
            const ringsHeight = parseInt(document.getElementById('rings_height').value);
            const spacingX = parseFloat(document.getElementById('spacing_x').value) * 10; // Convert cm to mm
            const spacingY = parseFloat(document.getElementById('spacing_y').value) * 10; // Convert cm to mm
            const radius = holeDiameter / 2;

            // Calculate ring positions - AROUND THE PERIMETER with equal spacing
            const rings = [];

            // Calculate perimeter length (excluding corners to avoid double counting)
            const topBottomLength = width - 2 * edgeDistance;
            const leftRightLength = height - 2 * edgeDistance;
            const perimeter = 2 * topBottomLength + 2 * leftRightLength;

            // Calculate spacing for each direction
            let actualSpacingX, actualSpacingY;

            if (spacingX === 0) {
                // Auto spacing X - distribute evenly along top and bottom edges
                actualSpacingX = topBottomLength / (ringsWidth - 1);
            } else {
                actualSpacingX = spacingX;
            }

            if (spacingY === 0) {
                // Auto spacing Y - distribute evenly along left and right edges
                actualSpacingY = leftRightLength / (ringsHeight - 1);
            } else {
                actualSpacingY = spacingY;
            }

            // Validate that rings don't overlap (only if all values are valid)
            const minSpacingX = (holeDiameter + 2); // Minimum spacing to avoid overlap
            const minSpacingY = (holeDiameter + 2);

            // Only show alert if values are complete and valid
            if (width > 0 && height > 0 && edgeDistance > 0 && holeDiameter > 0 &&
                ringsWidth >= 2 && ringsHeight >= 2 &&
                (actualSpacingX < minSpacingX || actualSpacingY < minSpacingY)) {
                alert(`מרווח קטן מדי! מינימום: ${(minSpacingX/10).toFixed(1)} ס"מ לרוחב, ${(minSpacingY/10).toFixed(1)} ס"מ לגובה`);
                return;
            }

        // Place rings around the perimeter with calculated spacing
        // Corners are ALWAYS at fixed positions (edgeDistance from edge)

        // Top edge (left to right) - including corners
        for (let i = 0; i < ringsWidth; i++) {
            const x = edgeDistance + (i * actualSpacingX);
            rings.push({
                x: x,
                y: edgeDistance
            });
        }

        // Right edge (top to bottom, excluding top corner which is already placed)
        for (let i = 1; i < ringsHeight; i++) {
            const y = edgeDistance + (i * actualSpacingY);
            rings.push({
                x: width - edgeDistance,
                y: y
            });
        }

        // Bottom edge (right to left, excluding right corner which is already placed)
        for (let i = ringsWidth - 2; i >= 0; i--) {
            const x = edgeDistance + (i * actualSpacingX);
            rings.push({
                x: x,
                y: height - edgeDistance
            });
        }

        // Left edge (bottom to top, excluding bottom and top corners which are already placed)
        for (let i = ringsHeight - 2; i > 0; i--) {
            const y = edgeDistance + (i * actualSpacingY);
            rings.push({
                x: edgeDistance,
                y: y
            });
        }

            const uniqueRings = rings;

            // Calculate preview size (max 400px, maintain aspect ratio)
            const maxSize = 400;
            const aspectRatio = width / height;
            let previewWidth, previewHeight;

            if (aspectRatio > 1) {
                previewWidth = maxSize;
                previewHeight = maxSize / aspectRatio;
            } else {
                previewHeight = maxSize;
                previewWidth = maxSize * aspectRatio;
            }

        // Generate SVG
        let circlesHTML = '';
        uniqueRings.forEach(ring => {
            circlesHTML += `<circle cx="${ring.x}" cy="${ring.y}" r="${radius}" fill="none" stroke="red" stroke-width="0.7"/>`;
        });

        const svg = `
            <svg width="${previewWidth}" height="${previewHeight}" viewBox="0 0 ${width} ${height}" xmlns="http://www.w3.org/2000/svg">
                <!-- מסגרת השלט -->
                <rect x="0" y="0" width="${width}" height="${height}" fill="none" stroke="black" stroke-width="0.7"/>

                <!-- טבעות -->
                ${circlesHTML}
            </svg>
        `;

            // Add dimensions info
            const dimensionsInfo = `
                <div class="mt-3">
                    <small class="text-muted">
                        <strong>מידות אמיתיות:</strong> ${(width/10).toFixed(1)} × ${(height/10).toFixed(1)} ס"מ<br>
                        <strong>תצוגה:</strong> ${previewWidth.toFixed(0)} × ${previewHeight.toFixed(0)} פיקסלים<br>
                        <strong>טבעות:</strong> ${ringsWidth} לרוחב × ${ringsHeight} לגובה = ${uniqueRings.length} טבעות מסביב<br>
                        <strong>מרווח לרוחב:</strong> ${(actualSpacingX/10).toFixed(1)} ס"מ<br>
                        <strong>מרווח לגובה:</strong> ${(actualSpacingY/10).toFixed(1)} ס"מ
                    </small>
                </div>
            `;

        // Preview is always visible now
        document.getElementById('previewContent').innerHTML = svg + dimensionsInfo;
        }

        function setupDownloadButtons() {
            // Download SVG button
            document.getElementById('downloadSvgBtn').onclick = function() {
                downloadSVG();
            };

            // Download PDF button
            document.getElementById('downloadPdfBtn').onclick = function() {
                downloadPDF();
            };

            // Copy SVG button
            document.getElementById('copySvgBtn').onclick = function() {
                copySVG();
            };
        }

        function downloadSVG() {
            if (!window.currentSvgContent) {
                alert('אין תוכן SVG להורדה');
                return;
            }

            const blob = new Blob([window.currentSvgContent], { type: 'image/svg+xml' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = window.currentFilename || 'windshield_sign.svg';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function downloadPDF() {
            if (!window.currentSvgContent) {
                alert('אין תוכן SVG להורדה');
                return;
            }

            // For now, we'll convert SVG to PDF using a simple approach
            // In a real implementation, you would use a library like jsPDF
            const svgData = window.currentSvgContent;
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();

            img.onload = function() {
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);

                canvas.toBlob(function(blob) {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = (window.currentFilename || 'windshield_sign').replace('.svg', '.pdf');
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                }, 'application/pdf');
            };

            const svgBlob = new Blob([svgData], { type: 'image/svg+xml' });
            const url = URL.createObjectURL(svgBlob);
            img.src = url;
        }

        function copySVG() {
            if (!window.currentSvgContent) {
                alert('אין תוכן SVG להעתקה');
                return;
            }

            navigator.clipboard.writeText(window.currentSvgContent).then(function() {
                alert('SVG הועתק ללוח');
            }).catch(function(err) {
                console.error('שגיאה בהעתקה: ', err);
                alert('שגיאה בהעתקה');
            });
        }
    </script>
</body>
</html>
