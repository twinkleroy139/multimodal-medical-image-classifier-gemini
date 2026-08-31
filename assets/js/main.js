document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const cameraInput = document.getElementById('camera-input');
    const previewContainer = document.getElementById('preview-container');
    const imagePreview = document.getElementById('image-preview');
    const uploadForm = document.getElementById('upload-form');
    const resultsContainer = document.getElementById('results-container');
    const resultContent = document.getElementById('result-content');
    const analyzeBtn = document.getElementById('analyze-btn');
    const resetBtn = document.getElementById('reset-btn');
    const overlayControlBox = document.getElementById('overlay-control-box');
    const toggleOverlayBtn = document.getElementById('toggleOverlayBtn');

    const overlayEngine = new ScanOverlayEngine('image-preview', 'overlayCanvas');

    if (dropZone) dropZone.addEventListener('click', () => fileInput.click());

    if (fileInput) {
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) handleFileSelection(e.target.files[0]);
        });
    }

    if (cameraInput) {
        cameraInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                fileInput.files = e.target.files;
                handleFileSelection(e.target.files[0]);
            }
        });
    }

    function handleFileSelection(file) {
        if (!file.type.match('image.*')) {
            alert('Please select a valid image file (.jpg, .png)');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreview.onload = () => {
                dropZone.classList.add('hidden');
                previewContainer.classList.remove('hidden');
                resultsContainer.classList.add('hidden');
                overlayControlBox.classList.add('hidden');
                overlayEngine.setBoxes([]);
                overlayEngine.draw();
            };
        }
        reader.readAsDataURL(file);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            uploadForm.reset();
            imagePreview.src = '#';
            previewContainer.classList.add('hidden');
            dropZone.classList.remove('hidden');
            resultsContainer.classList.add('hidden');
            overlayControlBox.classList.add('hidden');
            overlayEngine.setBoxes([]);
            overlayEngine.draw();
        });
    }

    if (toggleOverlayBtn) {
        toggleOverlayBtn.addEventListener('click', () => {
            const isActive = overlayEngine.toggle();
            toggleOverlayBtn.classList.toggle('active', isActive);
            toggleOverlayBtn.textContent = isActive ? '❌ Hide Overlay' : '🎯 Toggle Abnormalities Overlay';
        });
    }

    uploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(uploadForm);
        analyzeBtn.disabled = true;
        analyzeBtn.textContent = 'Analyzing Clinical Scan...';
        
        resultsContainer.classList.remove('hidden');
        resultContent.innerHTML = `<p style="color: var(--text-muted); text-align:center; padding:1rem;">Running neural classification model & generating diagnostic report...</p>`;

        try {
            const response = await fetch('process.php', { method: 'POST', body: formData });
            const data = await response.json();

            if (data.success) {
                const isAbnormal = data.abnormality_detected;
                const boundingBoxes = data.bounding_boxes || [];

                overlayEngine.setBoxes(boundingBoxes);
                if (boundingBoxes.length > 0) {
                    overlayControlBox.classList.remove('hidden');
                } else {
                    overlayControlBox.classList.add('hidden');
                }

                // Render Dual-Tab Diagnostic Interface
                resultContent.innerHTML = `
                    <div class="report-tabs">
                        <button type="button" class="report-tab-btn active" onclick="switchReportTab('patient-tab')">🗣️ Patient Summary</button>
                        <button type="button" class="report-tab-btn" onclick="switchReportTab('radiologist-tab')">🩺 Radiologist Report</button>
                    </div>

                    <div class="report-card" style="border-left: 4px solid ${isAbnormal ? 'var(--danger-color)' : 'var(--success-color)'};">
                        <h4 style="color: ${isAbnormal ? 'var(--danger-color)' : 'var(--success-color)'}; font-size: 1.15rem; margin-bottom: 0.75rem;">
                            ${data.title || 'Diagnostic Assessment Complete'}
                        </h4>

                        <!-- TAB 1: PATIENT SUMMARY -->
                        <div id="patient-tab" class="report-tab-pane">
                            <div class="metrics-grid">
                                <div class="metric-box">
                                    <p style="font-size: 0.8rem; color: var(--text-muted);">Confidence Score</p>
                                    <p style="font-size: 1.1rem; font-weight: 700;">${data.confidence}%</p>
                                </div>
                                <div class="metric-box">
                                    <p style="font-size: 0.8rem; color: var(--text-muted);">Severity Evaluation</p>
                                    <p style="font-size: 1.1rem; font-weight: 700; color: ${isAbnormal ? 'var(--danger-color)' : 'var(--success-color)'};">${data.severity}</p>
                                </div>
                            </div>

                            <div class="info-block">
                                <p style="font-weight: 700; color: var(--primary-light); margin-bottom: 0.25rem;">What This Means:</p>
                                <p style="font-size: 0.95rem; color: var(--text-main);">${data.patient_summary}</p>
                            </div>

                            <div class="info-block">
                                <p style="font-weight: 700; color: var(--primary-light); margin-bottom: 0.25rem;">Next Steps & Advice:</p>
                                <p style="font-size: 0.95rem; color: var(--text-main);">${data.suggestions}</p>
                            </div>
                        </div>

                        <!-- TAB 2: RADIOLOGIST TECHNICAL REPORT -->
                        <div id="radiologist-tab" class="report-tab-pane hidden">
                            <div class="info-block">
                                <p style="font-weight: 700; color: var(--primary-light); margin-bottom: 0.25rem;">Anatomical Region:</p>
                                <p style="font-size: 0.95rem;">${data.affected_area}</p>
                            </div>

                            <div class="info-block">
                                <p style="font-weight: 700; color: var(--primary-light); margin-bottom: 0.5rem;">Detailed Pathological Breakdown:</p>
                                <ul style="padding-left: 1.25rem; font-size: 0.9rem; color: var(--text-muted);">
                                    <li><strong style="color:var(--text-main)">Pathologies/Calcifications:</strong> ${data.clinical_findings?.pathologies_and_calcifications || 'None noted'}</li>
                                    <li><strong style="color:var(--text-main)">Skeletal Integrity:</strong> ${data.clinical_findings?.skeletal_integrity || 'Intact'}</li>
                                    <li><strong style="color:var(--text-main)">Soft Tissue/Organs:</strong> ${data.clinical_findings?.soft_tissue_and_organs || 'Unremarkable'}</li>
                                </ul>
                            </div>

                            <div class="info-block">
                                <p style="font-weight: 700; color: var(--primary-light); margin-bottom: 0.25rem;">Medications & Interventions:</p>
                                <p style="font-size: 0.95rem;">${data.medications}</p>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                resultContent.innerHTML = `<p style="color: var(--danger-color); text-align:center;">Error: ${data.message}</p>`;
            }
        } catch (error) {
            resultContent.innerHTML = `<p style="color: var(--danger-color); text-align:center;">Communication error with backend server.</p>`;
        } finally {
            analyzeBtn.disabled = false;
            analyzeBtn.textContent = 'Analyze Scan Report';
        }
    });
});

window.switchReportTab = function(tabId) {
    document.querySelectorAll('.report-tab-pane').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.report-tab-btn').forEach(el => el.classList.remove('active'));
    
    document.getElementById(tabId).classList.remove('hidden');
    event.currentTarget.classList.add('active');
};