## 5.2.1 Ciri-Ciri PB 350
Jadual 5: Ciri-Ciri PB 350

Bil Perkara Catatan

1 Warisan RRIM 600 × PB 235
2 Potensi hasil (kg/ha/th) 2,765
3 Anggaran hasil kayu (m³/pokok) 19T/1.6
4 Bentuk daun Bulat (rounded)
5 Bentuk hujung daun Kuspidat (Cuspidate)
6 Bentuk pangkal daun Bulat (Obtuse)
7 Kedudukan lai daun Bersentuh ke bertindih
8 Bentuk tepi daun Gelombang
9 Warna daun dan kilauan Hijau tua, sedikit berkilat
10 Permukaan daun Licin
11 Pandangan memanjang daun Rata/Selanjar
12 Pandangan melintang daun Rata
13 Saiz dan kedudukan gagang daun Sederhana panjang, rata
14 Saiz dan kedudukan anak gagang Pendek dan rata
15 Warna lateks Putih

## 5.1.1 Ciri-Ciri PB 260
Jadual 1: Ciri-Ciri PB 260

Bil Perkara Maklumat/Catatan

1 Warisan PB5/51 × PB49
2 Potensi hasil (kg/ha/th) 2,675
3 Anggaran hasil kayu (m³/pokok) 1.29/pokok
4 Bentuk daun Bujur telur (Obovate) ke Bujur sama (Elliptical)
5 Bentuk hujung daun Akuminat (Accuminate)
6 Bentuk pangkal daun Baji/Tirus (Cuneate)
7 Kedudukan lai daun Terpisah ke Bersentuhan
8 Bentuk tepi daun Keriting
9 Warna daun dan kilauan Hijau muda/kekuningan, sedikit berkilat
10 Permukaan daun Kasar
11 Pandangan memanjang daun Menurun
12 Pandangan melintang daun Bentuk perahu (boat shape)
13 Saiz dan kedudukan gagang daun Sederhana panjang dan rata
14 Saiz dan kedudukan anak gagang Sederhana panjang dan menurun
15 Warna lateks Krim

## 5.5 KLON RRIM 2002

5.5.1 Ciri-Ciri RRIM 2002

Jadual 17: Ciri-Ciri RRIM 2002

Bil Perkara Catatan

1 Warisan PB 5/51 × FORD 351
2 Potensi hasil (kg/ha/th) 2,348
3 Anggaran hasil kayu (m³/pokok) 17Th/1.10
4 Bentuk daun Bujur sama (Elliptical)
5 Bentuk hujung daun Akuminat (Acuminate)
6 Bentuk pangkal daun Bulat (Obtuse)
7 Kedudukan lai daun Bersentuhan ke bertindih
8 Bentuk tepi daun Licin
9 Warna daun dan kilauan Hijau muda, sedikit berkilat
10 Permukaan daun Licin
11 Pandangan memanjang daun Rata/Selanjar
12 Pandangan melintang daun Bentuk perahu (boat shape)
13 Saiz dan kedudukan gagang daun Sederhana panjang, rata
14 Saiz dan kedudukan anak gagang Pendek, rata
15 Warna lateks Kekuningan (yellowish)

API Implement from this website to apps
# API-Driven Clone Data — No Hardcoding

Replace all hardcoded clone/sample data with dynamic data fetched from the backend API. Admin can add, edit, and delete clone samples via the webserver.

## Sample.md — 3 Real Clones

The updated Sample.md now contains:
- **PB 350** (Warisan: RRIM 600 × PB 235, Potensi: 2,765 kg/ha/th)
- **PB 260** (Warisan: PB5/51 × PB49, Potensi: 2,675 kg/ha/th)
- **RRIM 2002** (Warisan: PB 5/51 × FORD 351, Potensi: 2,348 kg/ha/th)

---

## What You Need on the API Webserver (PHP + MySQL)

### 1. MySQL Table: `clone_samples`

```sql
CREATE TABLE clone_samples (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clone_name VARCHAR(50) NOT NULL,          -- e.g. "PB 350"
    warisan VARCHAR(100),                      -- e.g. "RRIM 600 × PB 235"
    potensi_hasil VARCHAR(50),                 -- e.g. "2,765"
    anggaran_kayu VARCHAR(50),                 -- e.g. "19T/1.6"
    bentuk_daun VARCHAR(100),                  -- e.g. "Bulat (rounded)"
    bentuk_hujung_daun VARCHAR(100),           -- e.g. "Kuspidat (Cuspidate)"
    bentuk_pangkal_daun VARCHAR(100),          -- e.g. "Bulat (Obtuse)"
    kedudukan_lai_daun VARCHAR(100),           -- e.g. "Bersentuh ke bertindih"
    bentuk_tepi_daun VARCHAR(100),             -- e.g. "Gelombang"
    warna_daun_kilauan VARCHAR(100),           -- e.g. "Hijau tua, sedikit berkilat"
    permukaan_daun VARCHAR(100),               -- e.g. "Licin"
    pandangan_memanjang VARCHAR(100),           -- e.g. "Rata/Selanjar"
    pandangan_melintang VARCHAR(100),           -- e.g. "Rata"
    saiz_gagang_daun VARCHAR(100),             -- e.g. "Sederhana panjang, rata"
    saiz_anak_gagang VARCHAR(100),             -- e.g. "Pendek dan rata"
    warna_lateks VARCHAR(100),                 -- e.g. "Putih"
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2. Seed Data (INSERT the 3 clones from Sample.md)

```sql
INSERT INTO clone_samples (clone_name, warisan, potensi_hasil, anggaran_kayu, bentuk_daun, bentuk_hujung_daun, bentuk_pangkal_daun, kedudukan_lai_daun, bentuk_tepi_daun, warna_daun_kilauan, permukaan_daun, pandangan_memanjang, pandangan_melintang, saiz_gagang_daun, saiz_anak_gagang, warna_lateks) VALUES
('PB 350', 'RRIM 600 × PB 235', '2,765', '19T/1.6', 'Bulat (rounded)', 'Kuspidat (Cuspidate)', 'Bulat (Obtuse)', 'Bersentuh ke bertindih', 'Gelombang', 'Hijau tua, sedikit berkilat', 'Licin', 'Rata/Selanjar', 'Rata', 'Sederhana panjang, rata', 'Pendek dan rata', 'Putih'),

('PB 260', 'PB5/51 × PB49', '2,675', '1.29/pokok', 'Bujur telur (Obovate) ke Bujur sama (Elliptical)', 'Akuminat (Accuminate)', 'Baji/Tirus (Cuneate)', 'Terpisah ke Bersentuhan', 'Keriting', 'Hijau muda/kekuningan, sedikit berkilat', 'Kasar', 'Menurun', 'Bentuk perahu (boat shape)', 'Sederhana panjang dan rata', 'Sederhana panjang dan menurun', 'Krim'),

('RRIM 2002', 'PB 5/51 × FORD 351', '2,348', '17Th/1.10', 'Bujur sama (Elliptical)', 'Akuminat (Acuminate)', 'Bulat (Obtuse)', 'Bersentuhan ke bertindih', 'Licin', 'Hijau muda, sedikit berkilat', 'Licin', 'Rata/Selanjar', 'Bentuk perahu (boat shape)', 'Sederhana panjang, rata', 'Pendek, rata', 'Kekuningan (yellowish)');
```

### 3. API Endpoints (4 endpoints — same pattern as your existing `api/analysis/*`)

| Method | URL | Auth | Description |
|--------|-----|------|-------------|
| `GET` | `api/clone-samples/list` | Bearer token | List all active clone samples |
| `POST` | `api/clone-samples/create` | Bearer token (admin) | Add a new clone sample |
| `POST` | `api/clone-samples/update` | Bearer token (admin) | Edit an existing clone sample |
| `DELETE` | `api/clone-samples/delete&id={id}` | Bearer token (admin) | Delete a clone sample |

#### GET `api/clone-samples/list` — Response format:
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "clone_name": "PB 350",
      "warisan": "RRIM 600 × PB 235",
      "potensi_hasil": "2,765",
      "anggaran_kayu": "19T/1.6",
      "bentuk_daun": "Bulat (rounded)",
      "bentuk_hujung_daun": "Kuspidat (Cuspidate)",
      "bentuk_pangkal_daun": "Bulat (Obtuse)",
      "kedudukan_lai_daun": "Bersentuh ke bertindih",
      "bentuk_tepi_daun": "Gelombang",
      "warna_daun_kilauan": "Hijau tua, sedikit berkilat",
      "permukaan_daun": "Licin",
      "pandangan_memanjang": "Rata/Selanjar",
      "pandangan_melintang": "Rata",
      "saiz_gagang_daun": "Sederhana panjang, rata",
      "saiz_anak_gagang": "Pendek dan rata",
      "warna_lateks": "Putih",
      "status": "active"
    }
  ]
}
```

---

## Flutter App Changes

### 1. New Model: `CloneSample`

#### [NEW] [clone_sample.dart](file:///home/maui/github/rubberclone-flutter/lib/data/models/clone_sample.dart)

A Dart model class with all 15 morphological fields matching the DB columns, plus `fromJson`/`toJson`.

---

### 2. New Service: `CloneService`

#### [NEW] [clone_service.dart](file:///home/maui/github/rubberclone-flutter/lib/data/services/clone_service.dart)

- `getCloneSamples()` → calls `GET api/clone-samples/list`, returns `List<CloneSample>`
- Follows exact same pattern as [analysis_service.dart](file:///home/maui/github/rubberclone-flutter/lib/data/services/analysis_service.dart) (uses `_baseUrl` from dotenv, Bearer token auth)

---

### 3. New Provider: `CloneProvider`

#### [NEW] [clone_provider.dart](file:///home/maui/github/rubberclone-flutter/lib/presentation/providers/clone_provider.dart)

- Holds `List<CloneSample> _clones` in memory
- `loadClones(token)` — fetches from API, caches in SharedPreferences
- Provides a method `buildAiPromptSection()` that generates the clone list text dynamically for the Gemini prompt

---

### 4. Modify GeminiService — Dynamic Prompts

#### [MODIFY] [gemini_service.dart](file:///home/maui/github/rubberclone-flutter/lib/data/services/gemini_service.dart)

- `analyzeLeafImage()` now accepts a `List<CloneSample>` parameter
- The prompt is built dynamically from the clone list instead of hardcoded clone names/characteristics
- `generateSmartRecommendation()` also accepts clone list to build dynamic prompt

---

### 5. Modify AnalysisProvider — Remove All Mock Data

#### [MODIFY] [analysis_provider.dart](file:///home/maui/github/rubberclone-flutter/lib/presentation/providers/analysis_provider.dart)

- **Delete lines 53-116** — Remove the entire hardcoded `RRIM 3001` mock fallback block
- If API returns empty and cache is empty → just show empty state, no fake data

---

### 6. Modify HomeScreen — Dynamic Bar Chart

#### [MODIFY] [home_screen.dart](file:///home/maui/github/rubberclone-flutter/lib/presentation/screens/home_screen.dart)

- **Lines 404-412**: Remove hardcoded `count3001`/`count2025` fallback values. Bar chart now shows real data only (from scan history), dynamically counting whichever clones appear
- **Lines 874-974 (`_CustomBarChart`)**: Refactor to accept a `Map<String, int>` of clone counts instead of two hardcoded clone names. Renders bars dynamically for all clones found in history
- Load clones from `CloneProvider` on init

---

### 7. Register CloneProvider in main.dart

#### [MODIFY] [main.dart](file:///home/maui/github/rubberclone-flutter/lib/main.dart)

- Add `CloneProvider` to the `MultiProvider` list
- Load clones on app startup (after auth)

---

### 8. Wire GeminiService calls to use clone data

#### [MODIFY] [home_screen.dart](file:///home/maui/github/rubberclone-flutter/lib/presentation/screens/home_screen.dart) & [scan_button.dart](file:///home/maui/github/rubberclone-flutter/lib/presentation/widgets/scan_button.dart)

- Where `GeminiService.analyzeLeafImage(base64Image)` is called, pass the clones from `CloneProvider`
- Where `GeminiService.generateSmartRecommendation(...)` is called, pass the clones from `CloneProvider`

---

## Verification Plan

### Automated Tests
```bash
cd /home/maui/github/rubberclone-flutter && flutter analyze
```

### Manual Verification
- Grep for any remaining hardcoded old clone names (`RRIM 3001`, `RRIM 2025`, `RRIM 600`, `PR 255`)
- Verify the API list endpoint returns clone data (once you set up the PHP endpoints)
