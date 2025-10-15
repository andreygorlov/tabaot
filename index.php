<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>שלטי שמשונית</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
        }
        #previewContent {
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
            border-radius: 0.375rem;
        }
        #previewContent svg {
            max-width: 100%;
            max-height: 100%;
            border: 1px solid #dee2e6;
            background: white;
        }
    </style>
</head>
<body>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="text-center mb-3">
                    <h3>שלטי שמשונית</h3>
                </div>

                <!-- Form -->
                <div class="card mb-3">
                    <div class="card-body p-3">
                        <form id="windshieldForm">
                            <!-- רוחב גובה -->
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="width" class="form-label small">רוחב (ס"מ)</label>
                                    <input type="number" class="form-control form-control-sm" id="width" value="200" min="1" max="2000" step="0.1">
                                </div>
                                <div class="col-md-6">
                                    <label for="height" class="form-label small">גובה (ס"מ)</label>
                                    <input type="number" class="form-control form-control-sm" id="height" value="100" min="1" max="2000" step="0.1">
                                </div>
                            </div>

                            <!-- מרחק מהקצה | קוטר חור -->
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="edge_distance" class="form-label small">מרחק מהקצה (ס"מ)</label>
                                    <input type="number" class="form-control form-control-sm" id="edge_distance" value="2" min="0.5" max="50" step="0.1">
                                </div>
                                <div class="col-md-6">
                                    <label for="hole_diameter" class="form-label small">קוטר חור (ס"מ)</label>
                                    <input type="number" class="form-control form-control-sm" id="hole_diameter" value="0.8" min="0.1" max="10" step="0.1">
                                </div>
                            </div>

                            <!-- טבעות לרוחב | טבעות לגובה -->
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="rings_width" class="form-label small">טבעות לרוחב</label>
                                    <input type="number" class="form-control form-control-sm" id="rings_width" value="5" min="2" max="20">
                                </div>
                                <div class="col-md-6">
                                    <label for="rings_height" class="form-label small">טבעות לגובה</label>
                                    <input type="number" class="form-control form-control-sm" id="rings_height" value="3" min="2" max="20">
                                </div>
                            </div>

                            <!-- מרווח רוחב | מרווח גובה -->
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="spacing_x" class="form-label small">מרווח רוחב (ס"מ)</label>
                                    <input type="number" class="form-control form-control-sm" id="spacing_x" value="49.0" min="0" max="200" step="0.1">
                                </div>
                                <div class="col-md-6">
                                    <label for="spacing_y" class="form-label small">מרווח גובה (ס"מ)</label>
                                    <input type="number" class="form-control form-control-sm" id="spacing_y" value="48.0" min="0" max="200" step="0.1">
                                </div>
                            </div>

                            <!-- חיפושים אחרונים -->
                            <div class="row mb-2">
                                <div class="col-12">
                                    <label class="form-label small">חיפושים אחרונים</label>
                                    <select class="form-select form-select-sm" id="recentSearches" onchange="loadFromRecentSearch()">
                                        <option value="">בחר חיפוש קודם...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- איפוס | הורד SVG -->
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="resetHolesOnly()">
                                        איפוס
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-primary btn-sm w-100" onclick="downloadSVGFile()">
                                        הורד SVG
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- תצוגה מקדימה -->
                <div id="preview" class="mt-3" style="display: block;">
                    <h6>תצוגה מקדימה</h6>
                    <div id="previewContent" class="border p-2 text-center bg-light" style="min-height: 200px;">
                        <p class="text-muted">טוען תצוגה מקדימה...</p>
                    </div>
                    <div id="dimensionsInfo" class="text-muted small mt-2"></div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variables
        let recentSearches = [];

        // Load default values
        function loadDefaultValues() {
            const targetSpacing = 50; // cm

            // Calculate optimal rings for default dimensions
            const defaultWidth = 200;
            const defaultHeight = 100;
            const edgeDistance = 2;

            const ringsWidth = Math.max(2, Math.round((defaultWidth - 2 * edgeDistance) / targetSpacing) + 1);
            const ringsHeight = Math.max(2, Math.round((defaultHeight - 2 * edgeDistance) / targetSpacing) + 1);

            const actualSpacingX = (defaultWidth - 2 * edgeDistance) / (ringsWidth - 1);
            const actualSpacingY = (defaultHeight - 2 * edgeDistance) / (ringsHeight - 1);

            document.getElementById('width').value = defaultWidth;
            document.getElementById('height').value = defaultHeight;
            document.getElementById('edge_distance').value = edgeDistance;
            document.getElementById('hole_diameter').value = 0.8;
            document.getElementById('rings_width').value = ringsWidth;
            document.getElementById('rings_height').value = ringsHeight;
            document.getElementById('spacing_x').value = actualSpacingX.toFixed(1);
            document.getElementById('spacing_y').value = actualSpacingY.toFixed(1);
        }

        // Update spacing when rings count changes
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

        // Update rings count when spacing changes
        function updateCountFromSpacing() {
            const width = parseFloat(document.getElementById('width').value) * 10; // Convert cm to mm
            const height = parseFloat(document.getElementById('height').value) * 10; // Convert cm to mm
            const edgeDistance = parseFloat(document.getElementById('edge_distance').value) * 10; // Convert cm to mm
            const spacingX = parseFloat(document.getElementById('spacing_x').value) * 10; // Convert cm to mm
            const spacingY = parseFloat(document.getElementById('spacing_y').value) * 10; // Convert cm to mm

            if (spacingX > 0) {
                const topBottomLength = width - 2 * edgeDistance;
                const ringsWidth = Math.max(2, Math.round(topBottomLength / spacingX) + 1);
                document.getElementById('rings_width').value = ringsWidth;
            }

            if (spacingY > 0) {
                const leftRightLength = height - 2 * edgeDistance;
                const ringsHeight = Math.max(2, Math.round(leftRightLength / spacingY) + 1);
                document.getElementById('rings_height').value = ringsHeight;
            }
        }

        // Update rings when dimensions change
        function updateRingsFromDimensions() {
            const targetSpacing = 50; // cm

            const currentWidth = parseFloat(document.getElementById('width').value);
            const currentHeight = parseFloat(document.getElementById('height').value);
            const edgeDistance = parseFloat(document.getElementById('edge_distance').value);

            const ringsWidth = Math.max(2, Math.round((currentWidth - 2 * edgeDistance) / targetSpacing) + 1);
            const ringsHeight = Math.max(2, Math.round((currentHeight - 2 * edgeDistance) / targetSpacing) + 1);

            document.getElementById('rings_width').value = ringsWidth;
            document.getElementById('rings_height').value = ringsHeight;
        }

        // Update preview and code
        function updatePreviewAndCode() {
            testPreview();
        }

        // Reset holes only
        function resetHolesOnly() {
            const targetSpacing = 50; // cm

            const currentWidth = parseFloat(document.getElementById('width').value);
            const currentHeight = parseFloat(document.getElementById('height').value);
            const edgeDistance = 2; // cm

            const ringsWidth = Math.max(2, Math.round((currentWidth - 2 * edgeDistance) / targetSpacing) + 1);
            const ringsHeight = Math.max(2, Math.round((currentHeight - 2 * edgeDistance) / targetSpacing) + 1);

            const actualSpacingX = (currentWidth - 2 * edgeDistance) / (ringsWidth - 1);
            const actualSpacingY = (currentHeight - 2 * edgeDistance) / (ringsHeight - 1);

            document.getElementById('edge_distance').value = edgeDistance;
            document.getElementById('hole_diameter').value = 0.8;
            document.getElementById('rings_width').value = ringsWidth;
            document.getElementById('rings_height').value = ringsHeight;
            document.getElementById('spacing_x').value = actualSpacingX.toFixed(1);
            document.getElementById('spacing_y').value = actualSpacingY.toFixed(1);

            updatePreviewAndCode();
        }

        // Recent searches functions
        function loadRecentSearches() {
            const stored = localStorage.getItem('windshield_recent_searches');
            if (stored) {
                recentSearches = JSON.parse(stored);
                updateRecentSearchesDropdown();
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

            // Remove duplicates
            recentSearches = recentSearches.filter(item =>
                !(item.width === data.width && item.height === data.height &&
                  item.rings_width === data.rings_width && item.rings_height === data.rings_height)
            );

            // Add to beginning
            recentSearches.unshift(data);

            // Keep only last 5
            recentSearches = recentSearches.slice(0, 5);

            // Save to localStorage
            localStorage.setItem('windshield_recent_searches', JSON.stringify(recentSearches));
            updateRecentSearchesDropdown();
        }

        function updateRecentSearchesDropdown() {
            const select = document.getElementById('recentSearches');
            select.innerHTML = '<option value="">בחר חיפוש קודם...</option>';

            recentSearches.forEach((search, index) => {
                const option = document.createElement('option');
                option.value = index;
                option.textContent = `${search.width}×${search.height} - ${search.rings_width}×${search.rings_height} טבעות (${search.timestamp})`;
                select.appendChild(option);
            });
        }

        function loadFromRecentSearch() {
            const select = document.getElementById('recentSearches');
            const index = parseInt(select.value);

            if (index >= 0 && index < recentSearches.length) {
                const data = recentSearches[index];

                document.getElementById('width').value = data.width;
                document.getElementById('height').value = data.height;
                document.getElementById('edge_distance').value = data.edge_distance;
                document.getElementById('hole_diameter').value = data.hole_diameter;
                document.getElementById('rings_width').value = data.rings_width;
                document.getElementById('rings_height').value = data.rings_height;
                document.getElementById('spacing_x').value = data.spacing_x;
                document.getElementById('spacing_y').value = data.spacing_y;

                updateSpacingFromCount();
                updatePreviewAndCode();
            }
        }

        function clearRecentSearches() {
            localStorage.removeItem('windshield_recent_searches');
            recentSearches = [];
            updateRecentSearchesDropdown();
        }

        // Preview function
        function testPreview() {
            try {
                const width = parseFloat(document.getElementById('width').value) * 10; // Convert cm to mm
                const height = parseFloat(document.getElementById('height').value) * 10; // Convert cm to mm
                const edgeDistance = parseFloat(document.getElementById('edge_distance').value) * 10; // Convert cm to mm
                const holeDiameter = parseFloat(document.getElementById('hole_diameter').value) * 10; // Convert cm to mm
                const ringsWidth = parseInt(document.getElementById('rings_width').value);
                const ringsHeight = parseInt(document.getElementById('rings_height').value);
                const spacingX = parseFloat(document.getElementById('spacing_x').value) * 10; // Convert cm to mm
                const spacingY = parseFloat(document.getElementById('spacing_y').value) * 10; // Convert cm to mm
                const radius = holeDiameter / 2;

                // Calculate spacing
                const topBottomLength = width - 2 * edgeDistance;
                const leftRightLength = height - 2 * edgeDistance;
                let actualSpacingX, actualSpacingY;

                if (spacingX === 0) {
                    actualSpacingX = topBottomLength / (ringsWidth - 1);
                } else {
                    actualSpacingX = spacingX;
                }

                if (spacingY === 0) {
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
                const rings = [];

                // Top edge (left to right) - including corners
                for (let i = 0; i < ringsWidth; i++) {
                    const x = edgeDistance + (i * actualSpacingX);
                    rings.push({
                        x: x,
                        y: edgeDistance
                    });
                }

                // Right edge (excluding top corner)
                for (let i = 1; i < ringsHeight; i++) {
                    const x = width - edgeDistance;
                    const y = edgeDistance + (i * actualSpacingY);
                    rings.push({
                        x: x,
                        y: y
                    });
                }

                // Bottom edge (excluding right corner)
                for (let i = ringsWidth - 2; i >= 0; i--) {
                    const x = edgeDistance + (i * actualSpacingX);
                    const y = height - edgeDistance;
                    rings.push({
                        x: x,
                        y: y
                    });
                }

                // Left edge (excluding bottom and top corners)
                for (let i = ringsHeight - 2; i > 0; i--) {
                    const x = edgeDistance;
                    const y = edgeDistance + (i * actualSpacingY);
                    rings.push({
                        x: x,
                        y: y
                    });
                }

                // Generate circles HTML
                let circlesHTML = '';
                rings.forEach(ring => {
                    circlesHTML += `<circle cx="${ring.x}" cy="${ring.y}" r="${radius}" fill="none" stroke="red" stroke-width="0.7"/>`;
                });

                // Calculate preview dimensions (scale to fit max 400px)
                const maxPreviewSize = 400;
                const aspectRatio = width / height;
                let previewWidth, previewHeight;

                if (aspectRatio > 1) {
                    previewWidth = maxPreviewSize;
                    previewHeight = maxPreviewSize / aspectRatio;
                } else {
                    previewHeight = maxPreviewSize;
                    previewWidth = maxPreviewSize * aspectRatio;
                }

                // Generate SVG
                const svg = `
                    <svg width="${previewWidth}" height="${previewHeight}" viewBox="0 0 ${width} ${height}" xmlns="http://www.w3.org/2000/svg">
                        <!-- מסגרת השלט -->
                        <rect x="0" y="0" width="${width}" height="${height}" fill="none" stroke="black" stroke-width="0.7"/>

                        <!-- טבעות -->
                        ${circlesHTML}
                    </svg>
                `;

                // Update preview
                document.getElementById('previewContent').innerHTML = svg;

                // Update dimensions info
                const totalRings = rings.length;
                const dimensionsInfo = `
                    <div class="d-flex flex-wrap gap-3">
                        <span><strong>מידות:</strong> ${(width/10).toFixed(1)} × ${(height/10).toFixed(1)} ס"מ</span>
                        <span><strong>טבעות:</strong> ${ringsWidth} × ${ringsHeight} (${totalRings} סה"כ)</span>
                        <span><strong>מרווח רוחב:</strong> ${(actualSpacingX/10).toFixed(1)} ס"מ</span>
                        <span><strong>מרווח גובה:</strong> ${(actualSpacingY/10).toFixed(1)} ס"מ</span>
                    </div>
                `;
                document.getElementById('dimensionsInfo').innerHTML = dimensionsInfo;

            } catch (error) {
                console.error('Error in testPreview:', error);
                document.getElementById('previewContent').innerHTML = '<p class="text-danger">שגיאה ביצירת התצוגה המקדימה</p>';
            }
        }

        // Download SVG function
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

        // Initialize page
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
            });
            document.getElementById('rings_height').addEventListener('change', function() {
                updateSpacingFromCount();
                updatePreviewAndCode();
            });

            // Listen to dimension changes
            ['width', 'height', 'edge_distance'].forEach(id => {
                document.getElementById(id).addEventListener('change', function() {
                    updateRingsFromDimensions(); // Update rings to maintain ~50cm spacing
                    updatePreviewAndCode();
                });
            });

            // Listen to hole diameter changes (doesn't affect rings count)
            document.getElementById('hole_diameter').addEventListener('input', function() {
                updatePreviewAndCode();
            });

            // Listen to spacing changes
            document.getElementById('spacing_x').addEventListener('change', function() {
                updateCountFromSpacing();
                updatePreviewAndCode();
            });
            document.getElementById('spacing_y').addEventListener('change', function() {
                updateCountFromSpacing();
                updatePreviewAndCode();
            });
        });
    </script>
</body>
</html>
