<?php $activePage = 'mri'; ?>
<?php include 'includes/header.php'; ?>

<h2>MRI Scan Analysis</h2>
<p class="subtitle">Upload high-resolution MRI scans (.jpg, .png) for soft tissue, joint, or neurological evaluation.</p>

<form id="upload-form" enctype="multipart/form-data">
    <input type="hidden" name="modality" value="mri">
    
    <div class="upload-container" id="drop-zone">
        <div class="upload-icon">🧠</div>
        <p><strong>Drag & drop your MRI scan here</strong>, or <span class="browse-link">browse files</span></p>
        <input type="file" id="file-input" name="medical_image" accept="image/png, image/jpeg" class="hidden">
    </div>

    <div class="action-bar-center">
        <label for="file-input" class="btn-secondary">📁 Choose File</label>
        <label class="btn-secondary">📸 Capture Camera <input type="file" id="camera-input" accept="image/*" capture="environment" class="hidden"></label>
    </div>

    <div id="preview-container" class="hidden">
        <div class="viewport-wrapper">
            <img id="image-preview" src="#" alt="MRI Preview">
            <canvas id="overlayCanvas"></canvas>
        </div>
        <div id="overlay-control-box" class="hidden">
            <button type="button" id="toggleOverlayBtn" class="btn-overlay-toggle">
                🎯 Toggle Abnormalities Overlay
            </button>
        </div>
        <div class="preview-actions">
            <button type="submit" class="btn" id="analyze-btn">Analyze MRI Report</button>
            <button type="button" class="btn-secondary" id="reset-btn">Change Image</button>
        </div>
    </div>
</form>

<div id="results-container" class="hidden">
    <h3>Clinical Diagnostic Report</h3>
    <div id="result-content"></div>
</div>

<?php include 'includes/footer.php'; ?>