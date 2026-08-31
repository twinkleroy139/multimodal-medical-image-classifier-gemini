<div align="center">

# ⚡ MediScan-Net — AI-Powered Multimodal Medical Image Classifier

[![Live Demo](https://img.shields.io/badge/Live-Demo-brightgreen?style=for-the-badge&logo=render)](https://multimodal-medical-image-classifier.onrender.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue?style=for-the-badge&logo=php)](https://php.net)
[![Powered by Gemini](https://img.shields.io/badge/Google%20Gemini-Vision%20API-orange?style=for-the-badge&logo=google)](https://ai.google.dev/)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)

*An advanced clinical diagnostic assistant leveraging computer vision and generative AI to instantly analyze X-Rays, MRIs, and CT scans with interactive abnormality bounding overlays.*

[Explore Live Application](https://multimodal-medical-image-classifier.onrender.com) · [Report Bug & Request Feature](https://github.com/your-username/multimodal-medical-image-classifier/issues)

</div>

---

## 💡 About The Project

**MediScan-Net** is a full-stack web application built to bridge the gap between advanced artificial intelligence and medical imaging diagnostics. Designed for high performance and responsiveness, it allows medical professionals and researchers to upload or capture scan imagery, process it securely through backend controllers, and receive rich clinical insights within seconds. 

By integrating the **Google Gemini Vision API**, the system goes beyond simple classification—it generates plain-English patient summaries, detailed radiologist breakdowns (categorizing calcifications, skeletal integrity, and soft tissue anomalies), and plots dynamic visual bounding boxes directly onto an HTML5 Canvas workspace.

---

## 🚀 Key Features & Capabilities

* **Multimodal Image Support:** Tailored analytical workflows for **X-Ray**, **MRI**, and **CT Scan** diagnostic modalities.
* **Interactive Canvas Bounding Overlays:** A custom JavaScript rendering engine that scales and plots precision bounding coordinates over detected pathologies, complete with a toggleable view.
* **Structured Clinical Intelligence Reports:** Automatically structures AI outputs into standardized medical schemas featuring:
  * Plain-English summaries for patient communication.
  * Deep-dive radiologist pathological breakdowns.
  * Anatomical region identification and severity level indexes.
  * Actionable medical next steps and medication guidance.
* **Asynchronous AJAX Workflow:** Eliminates page reloads during heavy image processing and supports direct mobile camera capture.
* **Robust Security & Error Handling:** Built-in MIME type validation, secure image sanitization, and fallback environment resolution via a custom `.env` parser.

---

## 🛠️ Technology Stack

* **Backend:** PHP 8.2 (Modular Architecture, cURL, Native Session & File Handling)
* **Frontend:** Modern HTML5, CSS3 (Dark Medical UI Theme), Vanilla JavaScript (AJAX & HTML5 Canvas API)
* **AI Engine:** Google Gemini Vision API (`gemini-2.5-flash` model endpoint)
* **Deployment & Hosting:** Render Cloud Platform (Apache / Debian Server Environment)

---

## 📂 Project Architecture

```text
MediScan-Net/
├── assets/
│   ├── css/
│   │   └── style.css            # Responsive dark-mode medical workspace UI
│   └── js/
│       ├── main.js              # Async form submission & state management
│       └── canvas-overlay.js    # HTML5 Canvas bounding box plotting engine
├── config/
│   └── app.php                  # Centralized application environment configurations
├── includes/
│   ├── header.php               # Modular navigation header component
│   └── footer.php               # Universal page footer component
├── services/
│   ├── EnvLoader.php            # Native .env parser with multi-alias fallback resolution
│   ├── ImageProcessor.php       # MIME validation, secure upload handling, & base64 encoder
│   └── GeminiClient.php         # Secure cURL bridge interfacing with Google Gemini Vision API
├── uploads/                     # Temporary processing directory for uploaded scans
├── .env.example                 # Environment configuration template
├── index.php                    # X-Ray Diagnostic Interface
├── mri.php                      # MRI Diagnostic Interface
├── ct.php                       # CT Diagnostic Interface
├── process.php                  # Core REST API controller routing backend validation & AI calls
└── README.md








⚙️ Getting Started Locally
Follow these steps to set up and run MediScan-Net on your local development machine (e.g., XAMPP or PHP built-in server).

Prerequisites:
1. PHP version 8.1 or higher.
2.Local server environment (XAMPP, WampServer, or Apache/Nginx)
3.A valid Google Gemini API Key.


Installation Steps:

1. Clone the Repository
-> git clone [https://github.com/your-username/multimodal-medical-image-classifier.git](https://github.com/your-username/multimodal-medical-image-classifier.git)
cd multimodal-medical-image-classifier

2.Configure Environment Variables(Create a local configuration file by copying the template)

-> cp .env.example .env

Open .env in your text editor and add your API credentials:
-> API_KEY=YOUR_ACTUAL_GEMINI_API_KEY
-> API_ENDPOINT=[https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent](https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent)

3. Launch the Local Server
-> php -S localhost:8000
Open your browser and navigate to http://localhost:8000



4. ☁️ Deployment (Render / Cloud)
-> Connect your repository to Render as a Web Service.

-> Select PHP environment.

-> Under Environment Variables in Render Dashboard, set:

API_KEY: Your Gemini API Key

API_ENDPOINT: https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent

-> Deploy!


⚠️ Clinical & Medical Disclaimer
Notice: MediScan-Net is developed strictly as an auxiliary tool for educational, research, and technical demonstration purposes[cite: 14]. It is not a certified medical device and must not substitute for professional medical advice, clinical diagnosis, or treatment regimens administered by qualified healthcare professionals[cite: 14].


👤 Author & Career Portfolio
Built with passion as part of full-stack web engineering and artificial intelligence integration showcases. Feel free to connect or explore more projects on GitHub[https://github.com/twinkleroy139].