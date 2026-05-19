<p align="center">
  <svg xmlns="http://www.w3.org/2000/svg" width="420" height="80" viewBox="0 0 420 80">
    <text x="50%" y="58" font-family="Georgia, 'Times New Roman', serif" font-size="52" font-weight="700" fill="#CC0000" text-anchor="middle" letter-spacing="4">ResQFlow</text>
  </svg>
</p>

<p align="center">
  <strong>AI-Powered Emergency Response & Family Health Intelligence Operating System</strong>
</p>

<p align="center">
  Realtime Emergency Dispatch &nbsp;•&nbsp; AI Medical Intelligence &nbsp;•&nbsp; Family Health Ecosystem &nbsp;•&nbsp; Tactical Command Infrastructure
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Status-Operational-brightgreen?style=flat-square" alt="Status">
  <img src="https://img.shields.io/badge/AI%20Intelligence-Active-blue?style=flat-square" alt="AI">
  <img src="https://img.shields.io/badge/Realtime-Connected-orange?style=flat-square" alt="Realtime">
  <img src="https://img.shields.io/badge/License-MIT-lightgrey?style=flat-square" alt="License">
</p>

---

## 🚨 About ResQFlow

ResQFlow is a production-grade AI-powered Emergency Response & Health Intelligence Platform designed to modernize emergency medical coordination, realtime ambulance dispatch, and longitudinal family health management.

The platform combines:

- 🚑 **Realtime Emergency Dispatch Infrastructure**
- 🧠 **AI Health Intelligence**
- 👨‍👩‍👧 **Family Health Ecosystem**
- 📡 **Live Tactical Tracking**
- 🏥 **Hospital Operations Command**
- ⚡ **WebSocket Synchronization**
- 🔬 **Medical Report Intelligence**
- 🛡️ **Clinical Safety & Governance**
- 📈 **Longitudinal Risk Intelligence**

ResQFlow transforms traditional emergency systems into a connected, intelligent, and observable healthcare operating system.

---

## ✨ Core Platform Capabilities

### 🚑 Emergency Dispatch Lifecycle

- SOS-based emergency triggering
- Nearest hospital intelligent assignment
- Ambulance fleet allocation
- Strict dispatch state machine
- Realtime ambulance tracking
- ETA calculation engine
- Tactical hospital coordination
- Emergency escalation protocols
- Multi-dashboard synchronization

#### Lifecycle Flow

```
pending → accepted → dispatched → en_route → arrived → completed
```

---

### 🧠 AI Health Intelligence Ecosystem

ResQFlow integrates real AI-driven medical intelligence powered by Groq LLM infrastructure.

| Feature | Description |
|---|---|
| Medical Report Analysis | AI-driven extraction and interpretation of uploaded reports |
| OCR Extraction Pipeline | Converts scanned documents into structured health data |
| Longitudinal Health Intelligence | Tracks health trends over time per member |
| Disease Risk Prediction | Flags elevated risks based on history and hereditary data |
| AI Medication Insights | Context-aware medication summaries and interactions |
| Emergency Preparedness Scoring | Calculates readiness index per family profile |
| Family Hereditary Intelligence | Shared risk graph across family members |
| Health Confidence Scoring | Assigns confidence levels to all AI outputs |
| Clinical Explainability Engine | Transparent reasoning for every AI recommendation |

---

### 👨‍👩‍👧 Family Health Ecosystem

Each family member operates as an independent health entity with shared hereditary intelligence.

- Family health profiles
- Dependent medical records
- AI risk analysis per member
- Shared hereditary graph
- Emergency guardian logic
- Household health clustering
- Independent medical vaults
- AI synchronization per profile

---

### 🏥 Hospital Operations Platform

A realtime tactical command system for hospitals and emergency responders.

- Emergency queue management
- Ambulance telemetry
- Fleet maintenance protocols
- Dispatch intelligence
- Mission replay system
- Operational heatmaps
- Incident reconstruction
- Tactical dashboards
- Live synchronization

---

### 📡 Realtime Infrastructure

Built using realtime broadcasting architecture.

- WebSocket synchronization
- Reverb broadcasting
- Echo listeners
- Signal resilience monitoring
- Polling fallback system
- Latency monitoring
- Realtime mission propagation

---

### 🔐 Security & Clinical Safety

ResQFlow is built with strict operational and medical safety layers.

- Role-based authorization
- Signed temporary URLs
- Private medical document storage
- SOS throttling
- Impersonation logging
- Audit trails
- Medical safety filtering
- Prompt sanitization
- DTO validation pipelines

---

### 📊 AI Intelligence Maturity

The intelligence layer includes:

- Longitudinal reasoning
- Stability smoothing
- Recommendation consistency
- Risk governance
- Confidence-aware recommendations
- False-positive suppression
- Progressive disclosure UI
- Explainable AI reasoning

---

## 🧬 AI Architecture

### Intelligence Engines

| Engine | Role |
|---|---|
| `GroqClient` | Core LLM communication layer |
| `OCRExtractionService` | Document parsing and text extraction |
| `MedicalAnalysisService` | Health report interpretation |
| `LongitudinalIntelligenceEngine` | Trend and timeline reasoning |
| `RiskGovernanceEngine` | Risk validation and safety filtering |
| `HealthConfidenceEngine` | Confidence scoring per output |
| `ContextualRecommendationEngine` | Personalized health recommendations |
| `FamilyHealthGraph` | Hereditary risk propagation |
| `MedicalSafetyService` | Clinical output safety enforcement |
| `MedicationIntelligenceService` | Medication-level AI insights |

---

## ⚙️ Tech Stack

### Backend
- **Laravel 12** — PHP 8.2
- **MySQL** — Primary database
- **Redis** — Caching & queue backend
- **Laravel Queues** — Background job processing
- **Laravel Broadcasting + Reverb** — WebSocket infrastructure

### Frontend
- **Blade** — Server-side templating
- **Alpine.js** — Lightweight reactivity
- **Tailwind CSS** — Utility-first styling
- **Leaflet Maps** — Live tracking visualization
- **Chart.js** — Health analytics charts
- **Glassmorphism UI Architecture**

### AI & Intelligence
- **Groq API** — LLM inference
- **Llama Models** — Core language models
- **OCR Extraction Pipeline**
- **DTO-based AI Validation**
- **Context Compression Engine**

---

## 🗺️ Platform Modules

### User Layer
- SOS Command Interface
- Live Emergency Tracking
- Medical Intelligence Dashboard
- Family Ecosystem
- Medical Vault
- AI Risk Monitoring

### Hospital Layer
- Tactical Queue
- Fleet Management
- Dispatch Operations
- Telemetry Monitoring
- Resource Coordination

### Admin Layer
- Command Center
- Heatmaps
- Mission Replay
- Infrastructure Health
- Signal Monitoring
- System Observability

---

## 📂 Medical Document Intelligence

- Secure medical vault
- AI report extraction
- PDF preview system
- OCR parsing
- Confidence-based enrichment
- AI synchronization
- Longitudinal storage

---

## 🚀 Installation

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/resqflow.git
cd resqflow
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Update `.env`:

```env
DB_DATABASE=resqflow
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Configure Groq AI

```env
GROQ_API_KEY=your_groq_api_key
```

### 6. Configure Broadcasting & Queue

```env
QUEUE_CONNECTION=database
BROADCAST_CONNECTION=reverb
CACHE_STORE=database
```

### 7. Run Migrations

```bash
php artisan migrate --seed
```

### 8. Start Services

```bash
# Queue Worker
php artisan queue:work

# Realtime Server
php artisan reverb:start

# Development Server
php artisan serve
npm run dev
```

---

## 🧪 Testing

### Run Test Suite

```bash
php artisan test
```

### AI Pipeline Testing

1. Open a Medical Profile
2. Upload a sample medical report
3. Queue worker processes OCR + AI analysis
4. Verify:
   - Risk radar updates
   - AI recommendations appear
   - Indicators synchronize
   - Medications populate
   - Preparedness score recalculates

---

## 📌 Key Features Summary

| Module | Features |
|---|---|
| **Emergency System** | SOS, Dispatch, Tracking, ETA, Lifecycle Engine |
| **AI Intelligence** | OCR, Risk Analysis, Recommendations, Trend Engine |
| **Family Ecosystem** | Family Profiles, Hereditary Risks, Shared Intelligence |
| **Hospital System** | Queue, Fleet, Telemetry, Tactical Dashboard |
| **Admin Control** | Heatmaps, Replay, Monitoring, Health Metrics |
| **Security** | Signed URLs, DTO Validation, Audit Logs |
| **Realtime** | Reverb, Echo, Polling Fallback, Signal Recovery |

---

## 📈 Platform Status

```
SYSTEM STATUS:        OPERATIONAL ✅
AI INTELLIGENCE:      ACTIVE      🧠
REALTIME SIGNAL:      CONNECTED   📡
EMERGENCY NETWORK:    SYNCHRONIZED 🚑
FAMILY HEALTH LAYER:  ACTIVE      👨‍👩‍👧
```

---

## 📌 Roadmap

- [ ] Wearable integrations
- [ ] Live vitals streaming
- [ ] Multi-language AI support
- [ ] Predictive emergency modeling
- [ ] Hospital AI triage
- [ ] National emergency scaling
- [ ] AI voice dispatch assistant
- [ ] Mobile applications
- [ ] Offline emergency resilience

---

## 🛡️ Medical Disclaimer

> ResQFlow AI is an **advisory intelligence system** and **NOT a replacement** for licensed medical professionals.
>
> All generated recommendations:
> - Pass through `MedicalSafetyService`
> - Include confidence-aware reasoning
> - Avoid diagnostic certainty
> - Prioritize clinically safe outputs
>
> **Always consult qualified healthcare providers for medical decisions.**

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

<p align="center">
  Built as a next-generation AI emergency response & family health intelligence infrastructure platform.
</p>
