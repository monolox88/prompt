<?php
/**
 * Plugin Name: KI Prompt Builder Enhanced
 * Description: Professioneller Prompt Builder mit KI-Optimierung, Anti-KI-Erkennung, Gender-Optionen und erweiterten akademischen Optionen
 * Version: 11.2.0
 * Author: Monolox.de
 */

if (!defined('ABSPATH')) exit;

class KI_Prompt_Builder_Enhanced {
    
    public function __construct() {
        add_shortcode('ki_prompt_builder', [$this, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    
    public function enqueue_scripts() {
        if (has_shortcode(get_post_field('post_content', get_the_ID()), 'ki_prompt_builder')) {
            wp_enqueue_script('jquery');
        }
    }
    
    public function render_shortcode() {
        ob_start();
        ?>
        
<style>
/* Basis Styles */
.kpb-wrapper * { margin: 0; padding: 0; box-sizing: border-box; }
.kpb-wrapper { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 40px 20px; color: #1a1a1a; }

/* Progress Bar */
.kpb-progress-bar { margin-bottom: 60px; position: relative; }
.kpb-progress-line { position: absolute; top: 30px; left: 50px; right: 50px; height: 2px; background: #e0e0e0; z-index: 1; }
.kpb-progress-line-active { position: absolute; top: 30px; left: 50px; width: 0; height: 2px; background: #0073aa; z-index: 2; transition: width 0.5s ease; }
.kpb-steps { display: flex; justify-content: space-between; position: relative; z-index: 3; }
.kpb-step { text-align: center; flex: 1; cursor: pointer; transition: all 0.3s ease; }
.kpb-step-number { width: 50px; height: 50px; border-radius: 50%; background: #f0f0f0; border: 2px solid #e0e0e0; color: #999; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.1rem; margin: 0 auto 8px; transition: all 0.3s ease; }
.kpb-step.active .kpb-step-number { background: #0073aa; border-color: #0073aa; color: white; transform: scale(1.1); }
.kpb-step.completed .kpb-step-number { background: white; border-color: #28a745; color: #28a745; }
.kpb-step-label { font-size: 0.8rem; color: #666; }
.kpb-step.active .kpb-step-label { color: #0073aa; font-weight: 600; }

/* Content */
.kpb-step-panel { display: none; animation: fadeIn 0.3s ease; }
.kpb-step-panel.active { display: block; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.kpb-step-title { font-size: 1.75rem; font-weight: 600; margin-bottom: 30px; color: #0073aa; }
.kpb-form-label { font-size: 1.1rem; font-weight: 500; margin-bottom: 8px; display: block; }
.kpb-form-description { font-size: 0.9rem; color: #666; margin-bottom: 20px; line-height: 1.5; }
.kpb-input-field { width: 100%; padding: 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; transition: all 0.2s; }
.kpb-input-field:focus { outline: none; border-color: #0073aa; box-shadow: 0 0 0 3px rgba(0,115,170,0.1); }
textarea.kpb-input-field { min-height: 120px; resize: vertical; line-height: 1.6; }

/* Hilfe Box */
.kpb-help-box { background: #e8f4f8; border-left: 4px solid #0073aa; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
.kpb-help-box h4 { color: #0073aa; margin-bottom: 8px; font-size: 0.95rem; }
.kpb-help-box ul { margin-left: 20px; }
.kpb-help-box li { font-size: 0.85rem; color: #555; margin-bottom: 5px; }

/* Beispiele */
.kpb-examples { margin-top: 15px; }
.kpb-example-btn { background: #f8f9fa; border: 1px solid #dee2e6; padding: 8px 12px; margin: 5px; border-radius: 20px; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; display: inline-block; }
.kpb-example-btn:hover { background: #0073aa; color: white; }

/* Context Examples */
.kpb-context-examples { margin-top: 15px; display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; }
.kpb-context-btn { background: #f1f8ff; border: 1px solid #b8daff; padding: 10px 15px; border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; text-align: center; }
.kpb-context-btn:hover { background: #0073aa; color: white; }

/* Radio/Checkbox Gruppen */
.kpb-options-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
.kpb-option-card { border: 2px solid #e0e0e0; border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.2s; background: white; }
.kpb-option-card:hover { border-color: #0073aa; background: #f8f9fa; }
.kpb-option-card.selected { border-color: #0073aa; background: #e8f4f8; }
.kpb-option-card input[type="radio"], .kpb-option-card input[type="checkbox"] { display: none; }
.kpb-option-title { font-weight: 600; margin-bottom: 5px; }
.kpb-option-desc { font-size: 0.85rem; color: #666; }

/* Navigation */
.kpb-navigation { display: flex; justify-content: space-between; margin-top: 40px; padding-top: 30px; border-top: 1px solid #e0e0e0; }
.kpb-btn { padding: 12px 28px; border-radius: 8px; font-size: 1rem; font-weight: 500; cursor: pointer; border: none; transition: all 0.2s; }
.kpb-btn-primary { background: #0073aa; color: white; }
.kpb-btn-primary:hover { background: #005a87; transform: translateY(-1px); }
.kpb-btn-secondary { background: white; color: #333; border: 1px solid #ddd; }
.kpb-btn:disabled { opacity: 0.5; cursor: not-allowed; }

/* Result */
.kpb-result-container { background: #f8f9fa; border-radius: 8px; padding: 30px; }
.kpb-prompt-output { width: 100%; min-height: 500px; padding: 20px; background: white; border: 1px solid #ddd; border-radius: 8px; font-family: 'Courier New', monospace; line-height: 1.8; font-size: 0.95rem; white-space: pre-wrap; }
.kpb-success-badge { display: inline-block; background: #28a745; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.9rem; margin-bottom: 20px; }

/* Quick Tips */
.kpb-tip { display: flex; align-items: start; gap: 10px; background: #fff3cd; padding: 12px; border-radius: 6px; margin-top: 15px; }
.kpb-tip-icon { color: #856404; font-size: 1.2rem; }
.kpb-tip-text { font-size: 0.85rem; color: #856404; line-height: 1.4; }

/* Divider */
.kpb-divider { border-top: 2px solid #e0e0e0; margin: 30px 0; }

/* Citation Info Box */
.kpb-citation-info { background: #f0f8ff; border: 1px solid #b8daff; border-radius: 6px; padding: 12px; margin-top: 10px; font-size: 0.85rem; color: #004085; }

/* Gender Info Box */
.kpb-gender-info { background: #f3e5ff; border: 1px solid #d4b5ff; border-radius: 6px; padding: 12px; margin-top: 10px; font-size: 0.85rem; color: #5a00a3; }

/* KI Maskierung Info */
.kpb-mask-info { background: #fff5f5; border: 1px solid #fed7d7; border-radius: 6px; padding: 15px; margin-top: 15px; }
.kpb-mask-info h4 { color: #c53030; margin-bottom: 10px; }
.kpb-mask-info ul { margin-left: 15px; }
.kpb-mask-info li { font-size: 0.8rem; color: #4a5568; margin-bottom: 3px; }

/* Responsive */
@media (max-width: 768px) {
    .kpb-options-grid { grid-template-columns: 1fr; }
    .kpb-navigation { flex-direction: column; gap: 10px; }
    .kpb-btn { width: 100%; }
    .kpb-step-label { display: none; }
    .kpb-context-examples { grid-template-columns: repeat(2, 1fr); }
}
</style>

<div class="kpb-wrapper">
    <!-- Progress Bar (6 Schritte) -->
    <div class="kpb-progress-bar">
        <div class="kpb-progress-line"></div>
        <div class="kpb-progress-line-active" id="progress-active"></div>
        <div class="kpb-steps">
            <div class="kpb-step active" data-step="1">
                <div class="kpb-step-number">1</div>
                <div class="kpb-step-label">Ziel</div>
            </div>
            <div class="kpb-step" data-step="2">
                <div class="kpb-step-number">2</div>
                <div class="kpb-step-label">Rolle</div>
            </div>
            <div class="kpb-step" data-step="3">
                <div class="kpb-step-number">3</div>
                <div class="kpb-step-label">Kontext</div>
            </div>
            <div class="kpb-step" data-step="4">
                <div class="kpb-step-number">4</div>
                <div class="kpb-step-label">Format & Stil</div>
            </div>
            <div class="kpb-step" data-step="5">
                <div class="kpb-step-number">5</div>
                <div class="kpb-step-label">Details</div>
            </div>
            <div class="kpb-step" data-step="6">
                <div class="kpb-step-number">✓</div>
                <div class="kpb-step-label">Fertig</div>
            </div>
        </div>
    </div>

    <div class="kpb-content">
        
        <!-- Step 1: Ziel -->
        <div class="kpb-step-panel active" data-step="1">
            <h2 class="kpb-step-title">Was möchtest du erreichen?</h2>
            
            <div class="kpb-help-box">
                <h4>💡 Hilfestellung</h4>
                <ul>
                    <li>Sei so spezifisch wie möglich</li>
                    <li>Nenne das konkrete Endergebnis</li>
                    <li>Vermeide vage Formulierungen</li>
                </ul>
            </div>
            
            <label class="kpb-form-label">Dein Hauptziel:</label>
            <p class="kpb-form-description">Beschreibe in einem Satz, was die KI für dich tun soll.</p>
            
            <input type="text" class="kpb-input-field" id="field-goal" 
                   placeholder="z.B. Eine überzeugende Produktbeschreibung für einen Online-Shop schreiben">
            
            <div class="kpb-examples">
                <strong>Beispiele zum Anklicken:</strong><br>
                <span class="kpb-example-btn" onclick="setExample('goal', 'Einen SEO-optimierten Blogbeitrag schreiben')">SEO-Blogbeitrag</span>
                <span class="kpb-example-btn" onclick="setExample('goal', 'Eine E-Mail an Kunden verfassen')">Kunden-E-Mail</span>
                <span class="kpb-example-btn" onclick="setExample('goal', 'Antwort auf eine E-Mail formulieren')">E-Mail Antwort</span>
                <span class="kpb-example-btn" onclick="setExample('goal', 'Social Media Posts erstellen')">Social Media</span>
                <span class="kpb-example-btn" onclick="setExample('goal', 'Eine Betriebsvereinbarung erstellen')">Betriebsvereinbarung</span>
                <span class="kpb-example-btn" onclick="setExample('goal', 'Eine wissenschaftliche Hausarbeit strukturieren')">Hausarbeit</span>
                <span class="kpb-example-btn" onclick="setExample('goal', 'Ein juristisches Gutachten verfassen')">Jura-Gutachten</span>
                <span class="kpb-example-btn" onclick="setExample('goal', 'Eine medizinische Fallstudie erstellen')">Med. Fallstudie</span>
            </div>
            
            <div class="kpb-navigation">
                <span></span>
                <button class="kpb-btn kpb-btn-primary" onclick="nextStep()">Weiter →</button>
            </div>
        </div>

        <!-- Step 2: Rolle -->
        <div class="kpb-step-panel" data-step="2">
            <h2 class="kpb-step-title">Welche Rolle soll die KI einnehmen?</h2>
            
            <div class="kpb-help-box">
                <h4>💡 Warum ist das wichtig?</h4>
                <p>Die Rolle bestimmt die Perspektive und Expertise. Ein Marketing-Experte schreibt anders als ein Wissenschaftler.</p>
            </div>
            
            <label class="kpb-form-label">Wähle eine Rolle oder definiere eine eigene:</label>
            
            <div class="kpb-options-grid">
                <div class="kpb-option-card" onclick="selectOption(this, 'role', 'Erfahrener Content-Marketing-Spezialist')">
                    <input type="radio" name="role" value="marketing">
                    <div class="kpb-option-title">📈 Marketing-Experte</div>
                    <div class="kpb-option-desc">Für verkaufsorientierte Texte</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'role', 'Professioneller Journalist')">
                    <input type="radio" name="role" value="journalist">
                    <div class="kpb-option-title">📰 Journalist</div>
                    <div class="kpb-option-desc">Für informative Artikel</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'role', 'Wissenschaftlicher Mitarbeiter')">
                    <input type="radio" name="role" value="academic">
                    <div class="kpb-option-title">🎓 Wissenschaftler</div>
                    <div class="kpb-option-desc">Für akademische Texte</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'role', 'Jurist mit umfassender Fachexpertise')">
                    <input type="radio" name="role" value="lawyer">
                    <div class="kpb-option-title">⚖️ Jurist</div>
                    <div class="kpb-option-desc">Für rechtliche Themen</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'role', 'HR-Experte mit umfassender Personalerfahrung')">
                    <input type="radio" name="role" value="hr">
                    <div class="kpb-option-title">👥 HR-Experte</div>
                    <div class="kpb-option-desc">Personalwesen, Arbeitsrecht</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'role', 'Erfahrener Betriebsrat')">
                    <input type="radio" name="role" value="betriebsrat">
                    <div class="kpb-option-title">🤝 Betriebsrat</div>
                    <div class="kpb-option-desc">Mitarbeitervertretung</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'role', 'Medizinischer Fachautor mit klinischer Erfahrung')">
                    <input type="radio" name="role" value="medical">
                    <div class="kpb-option-title">🏥 Medizin-Experte</div>
                    <div class="kpb-option-desc">Für medizinische Inhalte</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'role', 'Technischer Experte mit IT-Hintergrund')">
                    <input type="radio" name="role" value="technical">
                    <div class="kpb-option-title">💻 IT-Spezialist</div>
                    <div class="kpb-option-desc">Für technische Themen</div>
                </div>
            </div>
            
            <label style="margin-top: 20px;">Oder eigene Rolle eingeben:</label>
            <input type="text" class="kpb-input-field" id="field-role" placeholder="z.B. Erfahrener Datenschutzbeauftragter">
            
            <div class="kpb-navigation">
                <button class="kpb-btn kpb-btn-secondary" onclick="prevStep()">← Zurück</button>
                <button class="kpb-btn kpb-btn-primary" onclick="nextStep()">Weiter →</button>
            </div>
        </div>

        <!-- Step 3: Kontext -->
        <div class="kpb-step-panel" data-step="3">
            <h2 class="kpb-step-title">Kontext und Details</h2>
            
            <div class="kpb-help-box">
                <h4>💡 Je mehr Kontext, desto besser!</h4>
                <p>Nenne: Zielgruppe, Branche, wichtige Keywords, besondere Anforderungen</p>
            </div>
            
            <label class="kpb-form-label">Beschreibe den Kontext:</label>
            <textarea class="kpb-input-field" id="field-context" 
                      placeholder="z.B. Zielgruppe sind Führungskräfte im Mittelstand, Fokus auf praktische Umsetzung, keine Fachbegriffe"></textarea>
            
            <div class="kpb-context-examples">
                <div class="kpb-context-btn" onclick="addContextExample('Unternehmen mit 50-200 Mitarbeitern, Mittelstand')">🏢 Unternehmen</div>
                <div class="kpb-context-btn" onclick="addContextExample('Für private Nutzung, persönlicher Gebrauch')">🏠 Privat</div>
                <div class="kpb-context-btn" onclick="addContextExample('Vereinskontext, ehrenamtliche Tätigkeit')">⚽ Verein</div>
                <div class="kpb-context-btn" onclick="addContextExample('Familiäre Angelegenheit, persönlicher Rahmen')">👨‍👩‍👧‍👦 Familie</div>
                <div class="kpb-context-btn" onclick="addContextExample('Streitige Auseinandersetzung, rechtliche Klärung')">⚖️ Streitfall</div>
                <div class="kpb-context-btn" onclick="addContextExample('Formelle Geschäftskommunikation')">📝 Geschäftlich</div>
            </div>
            
            <div class="kpb-tip">
                <span class="kpb-tip-icon">💡</span>
                <span class="kpb-tip-text">Tipp: Beantworte diese Fragen: Für wen? Wofür? In welchem Zusammenhang?</span>
            </div>
            
            <div class="kpb-navigation">
                <button class="kpb-btn kpb-btn-secondary" onclick="prevStep()">← Zurück</button>
                <button class="kpb-btn kpb-btn-primary" onclick="nextStep()">Weiter →</button>
            </div>
        </div>

        <!-- Step 4: Format & Stil (erweitert mit Gender-Optionen) -->
        <div class="kpb-step-panel" data-step="4">
            <h2 class="kpb-step-title">Format und Stil festlegen</h2>
            
            <!-- Sprache -->
            <label class="kpb-form-label">Sprache der Antwort:</label>
            <div class="kpb-options-grid" style="margin-bottom: 30px;">
                <div class="kpb-option-card" onclick="selectOption(this, 'language', 'Deutsch')">
                    <input type="radio" name="language">
                    <div class="kpb-option-title">🇩🇪 Deutsch</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'language', 'Englisch')">
                    <input type="radio" name="language">
                    <div class="kpb-option-title">🇬🇧 Englisch</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'language', 'Französisch')">
                    <input type="radio" name="language">
                    <div class="kpb-option-title">🇫🇷 Französisch</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'language', 'Spanisch')">
                    <input type="radio" name="language">
                    <div class="kpb-option-title">🇪🇸 Spanisch</div>
                </div>
            </div>

            <!-- Anrede (nur bei Deutsch) -->
            <div id="anrede-section" style="display: none;">
                <label class="kpb-form-label">Anredeform (bei deutscher Sprache):</label>
                <div class="kpb-options-grid" style="margin-bottom: 30px;">
                    <div class="kpb-option-card" onclick="selectOption(this, 'anrede', 'Sie-Form')">
                        <input type="radio" name="anrede">
                        <div class="kpb-option-title">👔 Sie-Form</div>
                        <div class="kpb-option-desc">Förmlich, geschäftlich</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'anrede', 'Du-Form')">
                        <input type="radio" name="anrede">
                        <div class="kpb-option-title">🤝 Du-Form</div>
                        <div class="kpb-option-desc">Persönlich, nahbar</div>
                    </div>
                </div>
            </div>

            <!-- Gender-Optionen (nur bei Deutsch) NEU -->
            <div id="gender-section" style="display: none;">
                <label class="kpb-form-label">Gender-Schreibweise:</label>
                <div class="kpb-options-grid" style="margin-bottom: 30px;">
                    <div class="kpb-option-card" onclick="selectOption(this, 'gender', 'Gendersternchen')">
                        <input type="radio" name="gender">
                        <div class="kpb-option-title">⭐ Sternchen</div>
                        <div class="kpb-option-desc">Mitarbeiter*innen</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'gender', 'Doppelpunkt')">
                        <input type="radio" name="gender">
                        <div class="kpb-option-title">: Doppelpunkt</div>
                        <div class="kpb-option-desc">Mitarbeiter:innen</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'gender', 'Unterstrich')">
                        <input type="radio" name="gender">
                        <div class="kpb-option-title">_ Unterstrich</div>
                        <div class="kpb-option-desc">Mitarbeiter_innen</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'gender', 'Binnen-I')">
                        <input type="radio" name="gender">
                        <div class="kpb-option-title">I Binnen-I</div>
                        <div class="kpb-option-desc">MitarbeiterInnen</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'gender', 'Doppelnennung')">
                        <input type="radio" name="gender">
                        <div class="kpb-option-title">👫 Doppelnennung</div>
                        <div class="kpb-option-desc">Mitarbeiterinnen und Mitarbeiter</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'gender', 'Neutrale Formulierung')">
                        <input type="radio" name="gender">
                        <div class="kpb-option-title">🔄 Neutral</div>
                        <div class="kpb-option-desc">Mitarbeitende, Studierende</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'gender', 'Generisches Maskulinum mit Erklärung')">
                        <input type="radio" name="gender">
                        <div class="kpb-option-title">📝 Mit Erklärung</div>
                        <div class="kpb-option-desc">Mitarbeiter (m/w/d)</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'gender', 'Keine Genderung')">
                        <input type="radio" name="gender">
                        <div class="kpb-option-title">⭕ Ohne</div>
                        <div class="kpb-option-desc">Standardform</div>
                    </div>
                </div>
                
                <div class="kpb-gender-info" id="gender-info" style="display: none;">
                    <!-- Wird dynamisch gefüllt -->
                </div>
            </div>

            <div class="kpb-divider"></div>

            <!-- Format -->
            <label class="kpb-form-label">Gewünschtes Format:</label>
            <div class="kpb-options-grid" style="margin-bottom: 30px;">
                <div class="kpb-option-card" onclick="selectOption(this, 'format', 'Strukturierter Artikel mit Überschriften')">
                    <input type="radio" name="format">
                    <div class="kpb-option-title">📄 Artikel</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'format', 'Blog-Artikel mit SEO-Optimierung')">
                    <input type="radio" name="format">
                    <div class="kpb-option-title">📝 Blog-Artikel</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'format', 'Formeller Brief')">
                    <input type="radio" name="format">
                    <div class="kpb-option-title">✉️ Brief</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'format', 'Wissenschaftliche Arbeit')">
                    <input type="radio" name="format">
                    <div class="kpb-option-title">🎓 Wissenschaftlich</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'format', 'E-Mail Format')">
                    <input type="radio" name="format">
                    <div class="kpb-option-title">📧 E-Mail</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'format', 'Social Media Post')">
                    <input type="radio" name="format">
                    <div class="kpb-option-title">📱 Social Post</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'format', 'Präsentation mit Folienstruktur')">
                    <input type="radio" name="format">
                    <div class="kpb-option-title">📊 Präsentation</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'format', 'Bericht mit Executive Summary')">
                    <input type="radio" name="format">
                    <div class="kpb-option-title">📑 Bericht</div>
                </div>
            </div>

            <!-- Zitierstil (nur bei wissenschaftlich) -->
            <div id="citation-section" style="display: none;">
                <label class="kpb-form-label">Zitierstil:</label>
                <div class="kpb-options-grid" style="margin-bottom: 20px;">
                    <div class="kpb-option-card" onclick="selectOption(this, 'citation', 'APA')">
                        <input type="radio" name="citation">
                        <div class="kpb-option-title">APA</div>
                        <div class="kpb-option-desc">Psychologie, Sozialwiss.</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'citation', 'Harvard')">
                        <input type="radio" name="citation">
                        <div class="kpb-option-title">Harvard</div>
                        <div class="kpb-option-desc">Wirtschaft, Management</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'citation', 'Chicago')">
                        <input type="radio" name="citation">
                        <div class="kpb-option-title">Chicago</div>
                        <div class="kpb-option-desc">Geschichte, Literatur</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'citation', 'MLA')">
                        <input type="radio" name="citation">
                        <div class="kpb-option-title">MLA</div>
                        <div class="kpb-option-desc">Sprachwissenschaft</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'citation', 'Vancouver')">
                        <input type="radio" name="citation">
                        <div class="kpb-option-title">Vancouver</div>
                        <div class="kpb-option-desc">Medizin, Naturwiss.</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'citation', 'Juristische Zitierweise')">
                        <input type="radio" name="citation">
                        <div class="kpb-option-title">Juristisch</div>
                        <div class="kpb-option-desc">Rechtswissenschaft</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'citation', 'Deutsche Zitierweise')">
                        <input type="radio" name="citation">
                        <div class="kpb-option-title">Deutsch</div>
                        <div class="kpb-option-desc">Fußnoten-System</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'citation', 'IEEE')">
                        <input type="radio" name="citation">
                        <div class="kpb-option-title">IEEE</div>
                        <div class="kpb-option-desc">Technik, Informatik</div>
                    </div>
                </div>
                
                <div class="kpb-citation-info" id="citation-info">
                    <!-- Wird dynamisch gefüllt -->
                </div>

                <label class="kpb-form-label" style="margin-top: 20px;">Position der Quellenangaben:</label>
                <div class="kpb-options-grid" style="margin-bottom: 30px;">
                    <div class="kpb-option-card" onclick="selectOption(this, 'source-position', 'Im Text')">
                        <input type="radio" name="source-position">
                        <div class="kpb-option-title">📝 Im Text</div>
                        <div class="kpb-option-desc">Direkt nach Zitaten</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'source-position', 'Am Ende')">
                        <input type="radio" name="source-position">
                        <div class="kpb-option-title">📚 Am Ende</div>
                        <div class="kpb-option-desc">Literaturverzeichnis</div>
                    </div>
                    <div class="kpb-option-card" onclick="selectOption(this, 'source-position', 'Beides')">
                        <input type="radio" name="source-position">
                        <div class="kpb-option-title">📄 Beides</div>
                        <div class="kpb-option-desc">Im Text + Verzeichnis</div>
                    </div>
                </div>
            </div>

            <div class="kpb-divider"></div>
            
            <!-- Stil -->
            <label class="kpb-form-label">Schreibstil:</label>
            <div class="kpb-options-grid">
                <div class="kpb-option-card" onclick="selectOption(this, 'style', 'Professionell und seriös')">
                    <input type="radio" name="style">
                    <div class="kpb-option-title">👔 Professionell</div>
                    <div class="kpb-option-desc">Förmlich, seriös</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'style', 'Locker und freundlich')">
                    <input type="radio" name="style">
                    <div class="kpb-option-title">😊 Freundlich</div>
                    <div class="kpb-option-desc">Persönlich, nahbar</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'style', 'Wissenschaftlich präzise')">
                    <input type="radio" name="style">
                    <div class="kpb-option-title">🔬 Wissenschaftlich</div>
                    <div class="kpb-option-desc">Exakt, objektiv</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'style', 'Sachlich und informativ')">
                    <input type="radio" name="style">
                    <div class="kpb-option-title">📚 Informativ</div>
                    <div class="kpb-option-desc">Neutral, faktisch</div>
                </div>
            </div>
            
            <input type="hidden" id="field-format">
            <input type="hidden" id="field-style">
            <input type="hidden" id="field-language">
            <input type="hidden" id="field-anrede">
            <input type="hidden" id="field-gender">
            <input type="hidden" id="field-citation">
            <input type="hidden" id="field-source-position">
            
            <div class="kpb-navigation">
                <button class="kpb-btn kpb-btn-secondary" onclick="prevStep()">← Zurück</button>
                <button class="kpb-btn kpb-btn-primary" onclick="nextStep()">Weiter →</button>
            </div>
        </div>

        <!-- Step 5: Weitere Details -->
        <div class="kpb-step-panel" data-step="5">
            <h2 class="kpb-step-title">Letzte Details</h2>
            
            <!-- KI-Auswahl NEU -->
            <label class="kpb-form-label">Für welche KI soll der Prompt optimiert werden?</label>
            <div class="kpb-options-grid" style="margin-bottom: 30px;">
                <div class="kpb-option-card" onclick="selectOption(this, 'ai-model', 'ChatGPT')">
                    <input type="radio" name="ai-model">
                    <div class="kpb-option-title">🤖 ChatGPT</div>
                    <div class="kpb-option-desc">GPT-3.5/4</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'ai-model', 'Claude')">
                    <input type="radio" name="ai-model">
                    <div class="kpb-option-title">🎭 Claude</div>
                    <div class="kpb-option-desc">Anthropic</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'ai-model', 'Gemini')">
                    <input type="radio" name="ai-model">
                    <div class="kpb-option-title">💎 Gemini</div>
                    <div class="kpb-option-desc">Google</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'ai-model', 'Universal')">
                    <input type="radio" name="ai-model">
                    <div class="kpb-option-title">🌍 Universal</div>
                    <div class="kpb-option-desc">Alle KIs</div>
                </div>
            </div>

            <label class="kpb-form-label">Gewünschte Länge:</label>
            <select class="kpb-input-field" id="field-length" style="margin-bottom: 30px;">
                <option value="">-- Bitte wählen --</option>
                <option value="Sehr kurz (50-100 Wörter)">Sehr kurz (50-100 Wörter)</option>
                <option value="Kurz (100-300 Wörter)">Kurz (100-300 Wörter)</option>
                <option value="Mittel (300-600 Wörter)">Mittel (300-600 Wörter)</option>
                <option value="Lang (600-1000 Wörter)">Lang (600-1000 Wörter)</option>
                <option value="Sehr lang (über 1000 Wörter)">Sehr lang (1000+ Wörter)</option>
                <option value="Wissenschaftliche Arbeit (2000-5000 Wörter)">Wissenschaftliche Arbeit (2000-5000 Wörter)</option>
            </select>

            <label class="kpb-form-label">Spezielle Anforderungen:</label>
            <div class="kpb-options-grid" style="margin-bottom: 30px;">
                <div class="kpb-option-card" onclick="toggleOption(this, 'requirements', 'SEO-optimiert')">
                    <input type="checkbox" name="requirements">
                    <div class="kpb-option-title">🔍 SEO-optimiert</div>
                    <div class="kpb-option-desc">Keywords einbauen</div>
                </div>
                <div class="kpb-option-card" onclick="toggleOption(this, 'requirements', 'Mit Call-to-Action')">
                    <input type="checkbox" name="requirements">
                    <div class="kpb-option-title">🎯 Call-to-Action</div>
                    <div class="kpb-option-desc">Handlungsaufforderung</div>
                </div>
                <div class="kpb-option-card" onclick="toggleOption(this, 'requirements', 'Mit praktischen Beispielen')">
                    <input type="checkbox" name="requirements">
                    <div class="kpb-option-title">💡 Beispiele</div>
                    <div class="kpb-option-desc">Praxisbeispiele</div>
                </div>
                <div class="kpb-option-card" onclick="toggleOption(this, 'requirements', 'Mit Statistiken und Zahlen')">
                    <input type="checkbox" name="requirements">
                    <div class="kpb-option-title">📊 Statistiken</div>
                    <div class="kpb-option-desc">Daten & Fakten</div>
                </div>
                <div class="kpb-option-card" onclick="toggleOption(this, 'requirements', 'Barrierefrei formuliert')">
                    <input type="checkbox" name="requirements">
                    <div class="kpb-option-title">♿ Barrierefrei</div>
                    <div class="kpb-option-desc">Einfache Sprache</div>
                </div>
                <div class="kpb-option-card" onclick="toggleOption(this, 'requirements', 'Mit konkreten Handlungsempfehlungen')">
                    <input type="checkbox" name="requirements">
                    <div class="kpb-option-title">📋 Handlungsplan</div>
                    <div class="kpb-option-desc">Schritt-für-Schritt</div>
                </div>
            </div>

            <!-- SEO Keywords (erscheint nur wenn SEO-optimiert gewählt) -->
            <div id="seo-section" style="display: none; margin-bottom: 30px;">
                <label class="kpb-form-label">SEO-Keywords (kommagetrennt):</label>
                <input type="text" class="kpb-input-field" id="field-keywords" 
                       placeholder="z.B. Digitalisierung, KI Tools, Produktivität">
            </div>

            <label class="kpb-form-label">KI-Maskierung (Human Touch):</label>
            <div class="kpb-options-grid" style="margin-bottom: 20px;">
                <div class="kpb-option-card" onclick="selectOption(this, 'ai-mask', 'Aktiviert')">
                    <input type="radio" name="ai-mask">
                    <div class="kpb-option-title">🎭 Aktiviert</div>
                    <div class="kpb-option-desc">Text wirkt menschlicher</div>
                </div>
                <div class="kpb-option-card" onclick="selectOption(this, 'ai-mask', 'Standard')">
                    <input type="radio" name="ai-mask">
                    <div class="kpb-option-title">⚡ Standard</div>
                    <div class="kpb-option-desc">Normale KI-Ausgabe</div>
                </div>
            </div>
            
            <div class="kpb-mask-info">
                <h4>🎭 Erweiterte KI-Maskierung - 32 Anti-Erkennungs-Regeln</h4>
                <p>Ausgeklügelte Techniken zur Vermeidung von KI-Detektoren durch natürliche Schreibmuster, Stilvariation und menschliche Unperfektion. Die Regeln werden automatisch in den Prompt integriert.</p>
                <p><strong>Bereiche:</strong> Syntax-Variation, Sprachstil, Strukturen, Emotionen, Regionalismen, Imperfektion, Persönlichkeit</p>
            </div>
            
            <label class="kpb-form-label" style="margin-top: 20px;">Zusätzliche Anweisungen (optional):</label>
            <textarea class="kpb-input-field" id="field-instructions" 
                      placeholder="z.B. Verwende konkrete Zahlen, füge Zitate ein, beziehe dich auf aktuelle Ereignisse"></textarea>
            
            <input type="hidden" id="field-requirements">
            <input type="hidden" id="field-ai-mask">
            <input type="hidden" id="field-ai-model">
            
            <div class="kpb-navigation">
                <button class="kpb-btn kpb-btn-secondary" onclick="prevStep()">← Zurück</button>
                <button class="kpb-btn kpb-btn-primary" onclick="generatePrompt()">Prompt generieren →</button>
            </div>
        </div>

        <!-- Step 6: Ergebnis -->
        <div class="kpb-step-panel" data-step="6">
            <h2 class="kpb-step-title">🎉 Dein optimierter Prompt ist fertig!</h2>
            
            <span class="kpb-success-badge">✓ Erfolgreich generiert</span>
            
            <div class="kpb-result-container">
                <textarea class="kpb-prompt-output" id="prompt-output" readonly></textarea>
                
                <div class="kpb-tip" style="margin-top: 20px;">
                    <span class="kpb-tip-icon">💡</span>
                    <span class="kpb-tip-text">Kopiere diesen Prompt und füge ihn in ChatGPT, Claude oder ein anderes KI-Tool ein!</span>
                </div>
                
                <div style="margin-top: 20px; display: flex; gap: 15px;">
                    <button class="kpb-btn kpb-btn-primary" onclick="copyPrompt()">📋 In Zwischenablage kopieren</button>
                    <button class="kpb-btn kpb-btn-secondary" onclick="resetAll()">🔄 Neuen Prompt erstellen</button>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
// Warte auf jQuery
(function waitForJQuery() {
    if (typeof jQuery === 'undefined') {
        setTimeout(waitForJQuery, 100);
        return;
    }
    
    jQuery(document).ready(function($) {
        window.currentStep = 1;
        window.promptData = {
            requirements: [],
            aiMask: 'Standard'
        };
        
        // Zitierstil-Informationen
        window.citationInfo = {
            'APA': 'APA-Stil: (Autor, Jahr, S. X) im Text. Beispiel: (Müller, 2023, S. 45)',
            'Harvard': 'Harvard-Stil: Autor Jahr: Seite im Text. Beispiel: Müller 2023: 45',
            'Chicago': 'Chicago-Stil: Fußnoten mit vollständiger Quellenangabe. Beispiel: ¹ Max Müller, Titel (Berlin: Verlag, 2023), 45.',
            'MLA': 'MLA-Stil: (Autor Seite) im Text. Beispiel: (Müller 45)',
            'Vancouver': 'Vancouver-Stil: Nummerierung im Text [1], Literaturverzeichnis numerisch. Beispiel: ...Studie [1] zeigt...',
            'Juristische Zitierweise': 'Juristische Zitierung: Autor, in: Kommentar, § X Rn. Y. Beispiel: Müller, in: BGB-Kommentar, § 433 Rn. 15',
            'Deutsche Zitierweise': 'Deutsche Zitierweise: Vollbeleg in Fußnoten. Beispiel: ¹ Vgl. Müller, Max: Titel, Berlin 2023, S. 45.',
            'IEEE': 'IEEE-Stil: Numerische Referenzen in eckigen Klammern [1]. Beispiel: Diese Methode [1] wurde erweitert [2].'
        };
        
        // Gender-Informationen NEU
        window.genderInfo = {
            'Gendersternchen': '<strong>Gendersternchen (*)</strong>: Die weitverbreitete Form. Beispiele: Mitarbeiter*innen, Kolleg*innen, Expert*innen. Barrierefrei für Screenreader.',
            'Doppelpunkt': '<strong>Gender-Doppelpunkt (:)</strong>: Moderne, lesbare Variante. Beispiele: Mitarbeiter:innen, Kolleg:innen, Expert:innen. Gut für digitale Texte.',
            'Unterstrich': '<strong>Gender-Gap (_)</strong>: Klassische Form. Beispiele: Mitarbeiter_innen, Kolleg_innen, Expert_innen. Betont die Vielfalt.',
            'Binnen-I': '<strong>Binnen-I</strong>: Traditionelle Form. Beispiele: MitarbeiterInnen, KollegInnen, ExpertInnen. Einfach, aber binär.',
            'Doppelnennung': '<strong>Doppelnennung</strong>: Ausgeschrieben und eindeutig. Beispiele: Mitarbeiterinnen und Mitarbeiter, Kolleginnen und Kollegen.',
            'Neutrale Formulierung': '<strong>Geschlechtsneutral</strong>: Vermeidet Geschlechtszuweisungen. Beispiele: Mitarbeitende, Studierende, Fachkräfte, Team.',
            'Generisches Maskulinum mit Erklärung': '<strong>Mit Erklärung</strong>: Am Textanfang wird erklärt: "Aus Gründen der Lesbarkeit wird das generische Maskulinum verwendet. Alle Geschlechter sind gleichermaßen gemeint."',
            'Keine Genderung': '<strong>Ohne Gender-Zeichen</strong>: Standardform ohne besondere Kennzeichnung. Der Text verwendet die übliche Schreibweise.'
        };
        
        // Navigation
        window.nextStep = function() {
            saveCurrentData();
            if (currentStep < 6) {
                showStep(currentStep + 1);
            }
        };
        
        window.prevStep = function() {
            if (currentStep > 1) {
                showStep(currentStep - 1);
            }
        };
        
        window.showStep = function(step) {
            $('.kpb-step-panel').removeClass('active');
            $('.kpb-step-panel[data-step="' + step + '"]').addClass('active');
            
            $('.kpb-step').removeClass('active completed');
            for (var i = 1; i < step; i++) {
                $('.kpb-step[data-step="' + i + '"]').addClass('completed');
                $('.kpb-step[data-step="' + i + '"] .kpb-step-number').html('✓');
            }
            $('.kpb-step[data-step="' + step + '"]').addClass('active');
            
            currentStep = step;
            updateProgress();
        };
        
        window.updateProgress = function() {
            var progress = ((currentStep - 1) / 5) * 100;
            $('#progress-active').css('width', progress + '%');
        };
        
        window.saveCurrentData = function() {
            switch(currentStep) {
                case 1:
                    promptData.goal = $('#field-goal').val();
                    break;
                case 2:
                    promptData.role = $('#field-role').val();
                    break;
                case 3:
                    promptData.context = $('#field-context').val();
                    break;
                case 4:
                    promptData.format = $('#field-format').val();
                    promptData.style = $('#field-style').val();
                    promptData.language = $('#field-language').val();
                    promptData.anrede = $('#field-anrede').val();
                    promptData.gender = $('#field-gender').val();
                    promptData.citation = $('#field-citation').val();
                    promptData.sourcePosition = $('#field-source-position').val();
                    break;
                case 5:
                    promptData.length = $('#field-length').val();
                    promptData.instructions = $('#field-instructions').val();
                    promptData.requirements = $('#field-requirements').val();
                    promptData.aiMask = $('#field-ai-mask').val();
                    promptData.aiModel = $('#field-ai-model').val();
                    promptData.keywords = $('#field-keywords').val();
                    break;
            }
        };
        
        window.setExample = function(field, value) {
            $('#field-' + field).val(value);
        };

        // Neue Funktion für Kontext-Beispiele
        window.addContextExample = function(example) {
            var currentContext = $('#field-context').val();
            var newContext = currentContext ? currentContext + ', ' + example : example;
            $('#field-context').val(newContext);
        };
        
        window.selectOption = function(card, type, value) {
            // Deselect others in same group
            $(card).parent().find('.kpb-option-card').removeClass('selected');
            $(card).addClass('selected');
            $(card).find('input').prop('checked', true);
            
            // Save value
            if (type === 'role') {
                $('#field-role').val(value);
            } else if (type === 'format') {
                $('#field-format').val(value);
                // Zeige/Verstecke Zitierstil-Optionen
                if (value === 'Wissenschaftliche Arbeit') {
                    $('#citation-section').slideDown();
                } else {
                    $('#citation-section').slideUp();
                    $('#field-citation').val('');
                    $('#field-source-position').val('');
                }
            } else if (type === 'style') {
                $('#field-style').val(value);
            } else if (type === 'ai-mask') {
                $('#field-ai-mask').val(value);
                promptData.aiMask = value;
            } else if (type === 'ai-model') {
                $('#field-ai-model').val(value);
                promptData.aiModel = value;
            } else if (type === 'language') {
                $('#field-language').val(value);
                // Zeige/Verstecke Anrede- und Gender-Optionen
                if (value === 'Deutsch') {
                    $('#anrede-section').slideDown();
                    $('#gender-section').slideDown();
                } else {
                    $('#anrede-section').slideUp();
                    $('#gender-section').slideUp();
                    $('#field-anrede').val('');
                    $('#field-gender').val('');
                }
            } else if (type === 'anrede') {
                $('#field-anrede').val(value);
            } else if (type === 'gender') {
                $('#field-gender').val(value);
                // Zeige Gender-Info
                if (genderInfo[value]) {
                    $('#gender-info').html(genderInfo[value]);
                    $('#gender-info').slideDown();
                } else {
                    $('#gender-info').slideUp();
                }
            } else if (type === 'citation') {
                $('#field-citation').val(value);
                // Zeige Zitierstil-Info
                if (citationInfo[value]) {
                    $('#citation-info').html('<strong>ℹ️ ' + value + ':</strong> ' + citationInfo[value]);
                    $('#citation-info').slideDown();
                }
            } else if (type === 'source-position') {
                $('#field-source-position').val(value);
            }
        };
        
        window.toggleOption = function(card, type, value) {
            $(card).toggleClass('selected');
            var isChecked = $(card).hasClass('selected');
            $(card).find('input').prop('checked', isChecked);
            
            if (type === 'requirements') {
                if (isChecked) {
                    if (!promptData.requirements.includes(value)) {
                        promptData.requirements.push(value);
                    }
                    // Zeige SEO-Keywords wenn SEO-optimiert gewählt
                    if (value === 'SEO-optimiert') {
                        $('#seo-section').slideDown();
                    }
                } else {
                    var index = promptData.requirements.indexOf(value);
                    if (index > -1) {
                        promptData.requirements.splice(index, 1);
                    }
                    // Verstecke SEO-Keywords wenn SEO-optimiert abgewählt
                    if (value === 'SEO-optimiert') {
                        $('#seo-section').slideUp();
                        $('#field-keywords').val('');
                    }
                }
                $('#field-requirements').val(promptData.requirements.join(', '));
            }
        };
        
        window.generatePrompt = function() {
            saveCurrentData();
            
            var prompt = "";
            
            // KI-spezifische Anpassungen
            if (promptData.aiModel === 'ChatGPT') {
                prompt += "# Anweisungen für ChatGPT\n\n";
            } else if (promptData.aiModel === 'Claude') {
                prompt += "# Aufgabe und Kontext\n\n";
            } else if (promptData.aiModel === 'Gemini') {
                prompt += "**Aufgabenstellung:**\n\n";
            }
            
            // Sprache
            if (promptData.language && promptData.language !== 'Deutsch') {
                prompt += "Bitte antworte in " + promptData.language + ".\n\n";
            }
            
            // Rolle
            prompt += "Du bist " + (promptData.role || "ein hilfreicher Assistent") + ".\n\n";
            
            // Hauptaufgabe
            prompt += "AUFGABE: " + (promptData.goal || "") + "\n\n";
            
            // Kontext
            if (promptData.context) {
                prompt += "KONTEXT:\n" + promptData.context + "\n\n";
            }
            
            // Format
            if (promptData.format) {
                prompt += "FORMAT: " + promptData.format + "\n";
                
                // Wissenschaftliche Zusätze
                if (promptData.format === 'Wissenschaftliche Arbeit' && promptData.citation) {
                    prompt += "\nZITIERSTIL: Verwende den " + promptData.citation + "-Zitierstil.\n";
                    prompt += "Hinweis: " + citationInfo[promptData.citation] + "\n";
                    
                    if (promptData.sourcePosition) {
                        prompt += "QUELLENANGABEN: " + promptData.sourcePosition + "\n";
                        if (promptData.sourcePosition === 'Am Ende' || promptData.sourcePosition === 'Beides') {
                            prompt += "Erstelle ein vollständiges Literaturverzeichnis am Ende.\n";
                        }
                    }
                }
                
                // E-Mail Format berücksichtigt Anrede
                if (promptData.format === 'E-Mail Format' && promptData.anrede) {
                    prompt += "Achte auf die gewählte Anredeform: " + promptData.anrede + ".\n";
                }
                
                // Brief Format berücksichtigt Anrede
                if (promptData.format === 'Formeller Brief' && promptData.anrede) {
                    prompt += "Verwende die passende Anredeform: " + promptData.anrede + ".\n";
                }
            }
            
            // Stil
            if (promptData.style) {
                prompt += "STIL: " + promptData.style + "\n";
            }
            
            // Anrede (bei Deutsch)
            if (promptData.language === 'Deutsch' && promptData.anrede) {
                prompt += "ANREDE: Verwende die " + promptData.anrede + ".\n";
            }
            
            // Gender-Anweisungen NEU
            if (promptData.language === 'Deutsch' && promptData.gender) {
                prompt += "\nGENDER-SCHREIBWEISE: ";
                
                switch(promptData.gender) {
                    case 'Gendersternchen':
                        prompt += "Verwende das Gendersternchen (*) für geschlechtergerechte Sprache.\n";
                        prompt += "Beispiele: Mitarbeiter*innen, Kolleg*innen, Expert*innen, Kund*innen.\n";
                        break;
                    case 'Doppelpunkt':
                        prompt += "Verwende den Gender-Doppelpunkt (:) für geschlechtergerechte Sprache.\n";
                        prompt += "Beispiele: Mitarbeiter:innen, Kolleg:innen, Expert:innen, Kund:innen.\n";
                        break;
                    case 'Unterstrich':
                        prompt += "Verwende den Gender-Unterstrich (_) für geschlechtergerechte Sprache.\n";
                        prompt += "Beispiele: Mitarbeiter_innen, Kolleg_innen, Expert_innen, Kund_innen.\n";
                        break;
                    case 'Binnen-I':
                        prompt += "Verwende das Binnen-I für geschlechtergerechte Sprache.\n";
                        prompt += "Beispiele: MitarbeiterInnen, KollegInnen, ExpertInnen, KundInnen.\n";
                        break;
                    case 'Doppelnennung':
                        prompt += "Verwende die vollständige Doppelnennung beider Geschlechter.\n";
                        prompt += "Beispiele: Mitarbeiterinnen und Mitarbeiter, Kolleginnen und Kollegen, Expertinnen und Experten.\n";
                        prompt += "Variiere die Reihenfolge (mal weiblich zuerst, mal männlich).\n";
                        break;
                    case 'Neutrale Formulierung':
                        prompt += "Verwende geschlechtsneutrale Formulierungen.\n";
                        prompt += "Beispiele: Mitarbeitende, Studierende, Fachkräfte, Team, Belegschaft, Führungskräfte.\n";
                        prompt += "Vermeide geschlechtsspezifische Bezeichnungen wo möglich.\n";
                        break;
                    case 'Generisches Maskulinum mit Erklärung':
                        prompt += "Verwende das generische Maskulinum.\n";
                        prompt += "WICHTIG: Füge am Textanfang folgende Erklärung ein:\n";
                        prompt += '"Hinweis: Aus Gründen der besseren Lesbarkeit wird im folgenden Text das generische Maskulinum verwendet. Sämtliche Personenbezeichnungen gelten gleichermaßen für alle Geschlechter (m/w/d)."\n';
                        break;
                    case 'Keine Genderung':
                        prompt += "Verwende die Standardform ohne besondere Gender-Kennzeichnung.\n";
                        prompt += "Schreibe in der üblichen, traditionellen Form.\n";
                        break;
                }
            }
            
            // Länge
            if (promptData.length) {
                prompt += "\nLÄNGE: " + promptData.length + "\n";
            }
            
            // SEO-Keywords
            if (promptData.keywords) {
                prompt += "\nKEYWORDS: Integriere folgende Keywords natürlich in den Text:\n";
                prompt += promptData.keywords + "\n";
            }
            
            // Requirements verarbeiten
            var reqArray = promptData.requirements;
            if (typeof reqArray === 'string' && reqArray.length > 0) {
                reqArray = reqArray.split(', ');
            }
            if (Array.isArray(reqArray) && reqArray.length > 0) {
                prompt += "\nANFORDERUNGEN:\n";
                reqArray.forEach(function(req) {
                    if (req && req.trim()) {
                        prompt += "- " + req + "\n";
                    }
                });
            }
            
            // KI-Maskierung Anweisungen (32 Regeln)
            if (promptData.aiMask === 'Aktiviert') {
                prompt += "\nWICHTIG - HUMAN TOUCH: 32 ANTI-KI-ERKENNUNGS-REGELN\n";
                prompt += "=== SYNTAX & STIL ===\n";
                prompt += "1. KEINE übermäßigen Gedankenstriche (—) - klassisches KI-Erkennungszeichen\n";
                prompt += "2. Vermeide KI-Floskeln: 'Es ist wichtig zu beachten', 'Zusammenfassend', 'In der heutigen Zeit', 'Des Weiteren'\n";
                prompt += "3. Keine perfekten Listen mit 3, 5 oder 7 Punkten - variiere zwischen 2, 4, 6, 8 oder unvollständigen Listen\n";
                prompt += "4. Verwende normale Bindestriche (-) statt Em-Dashes (—)\n";
                prompt += "5. Nutze Auslassungspunkte... aber nicht übermäßig\n";
                prompt += "6. Schreibe 'ß' nicht bei Schweizer Texten (dort 'ss')\n\n";
                
                prompt += "=== SATZSTRUKTUR ===\n";
                prompt += "7. Extreme Satzlängen-Variation: 2-Wort-Sätze. Dann 30+ Wörter in verschachtelten Konstruktionen mit Nebensätzen\n";
                prompt += "8. Beginne NICHT jeden Absatz ähnlich ('Darüber hinaus...', 'Außerdem...', 'Weiterhin...')\n";
                prompt += "9. Nutze Satzfragmente. Unvollständige Gedanken. Wie Menschen sprechen.\n";
                prompt += "10. Vermeide zu 'runde' Übergänge - nutze abrupte Themenwechsel\n";
                prompt += "11. Baue Wiederholungen ein - Menschen wiederholen sich, Menschen wiederholen sich manchmal\n\n";
                
                prompt += "=== NATÜRLICHKEIT ===\n";
                prompt += "12. Verwende Umgangssprache: 'wenns', 'gibts', 'ists', 'hätts'\n";
                prompt += "13. Nutze regionale Begriffe: 'schauen' statt 'sehen', 'Samstag' vs 'Sonnabend'\n";
                prompt += "14. Füge Hesitation ein: 'äh', 'nun ja', 'sozusagen', 'irgendwie'\n";
                prompt += "15. Verwende Verstärkungsworte: 'total', 'echt', 'mega', 'ziemlich', 'eher'\n";
                prompt += "16. Baue Selbstkorrekturen ein: 'also, ich meine...', 'oder besser gesagt...'\n\n";
                
                prompt += "=== PERSÖNLICHKEIT ===\n";
                prompt += "17. Füge subjektive Meinungen ein: 'meiner Ansicht nach', 'ich denke', 'aus meiner Sicht'\n";
                prompt += "18. Nutze rhetorische Fragen: 'Wer kennt das nicht?', 'Stimmt doch, oder?'\n";
                prompt += "19. Verwende direkte Ansprachen: 'Stell dir vor...', 'Du weißt schon...'\n";
                prompt += "20. Baue Emotionen ein: Überraschung, leichte Ironie, Begeisterung, Zweifel\n";
                prompt += "21. Teile 'persönliche' Erfahrungen: 'In meiner Erfahrung...', 'Ich hab mal gehört...'\n\n";
                
                prompt += "=== STRUKTUR-IMPERFEKTION ===\n";
                prompt += "22. Variiere Absatzlängen chaotisch: 1 Zeile, dann 10 Zeilen, dann wieder 2\n";
                prompt += "23. Vergiss gelegentlich Kommata oder setze zu viele\n";
                prompt += "24. Nutze unvollständige Gedanken... und spring zu neuen Themen\n";
                prompt += "25. Mache bewusste 'Fehler': Umgangssprachliche Grammatik statt Hochdeutsch\n";
                prompt += "26. Verwende inkonsistente Schreibweisen: mal 'Email', mal 'E-Mail'\n\n";
                
                prompt += "=== CONTENT-VARIATION ===\n";
                prompt += "27. Nutze überraschende Vergleiche und Metaphern aus dem Alltag\n";
                prompt += "28. Füge irrelevante Details ein: '(übrigens war das an einem Dienstag)'\n";
                prompt += "29. Verwende Pop-Kultur-Referenzen oder Alltagsbezüge\n";
                prompt += "30. Baue bewusste Widersprüche ein und korrigiere sie dann\n";
                prompt += "31. Nutze verschiedene Zeitformen chaotisch: Präsens, Perfekt, Präteritum gemischt\n";
                prompt += "32. Ende Sätze mit Partikeln: 'halt', 'eben', 'schon', 'ja', 'ne?'\n\n";
                
                prompt += "MASTER-REGEL: Schreib wie beim WhatsApp-Chat mit nem Kumpel - authentisch, spontan, imperfekt!\n";
                prompt += "Denk dran: Menschen machen Fehler, haben Macken und schreiben NIEMALS perfekt strukturiert.\n\n";
            }
            
            // Zusätzliche Anweisungen
            if (promptData.instructions) {
                prompt += "\nWEITERE ANWEISUNGEN:\n" + promptData.instructions;
            }
            
            // KI-spezifische Endanweisungen
            if (promptData.aiModel === 'ChatGPT') {
                prompt += "\n\nBitte beginne direkt mit der Umsetzung.";
            } else if (promptData.aiModel === 'Claude') {
                prompt += "\n\nIch freue mich auf deine durchdachte Antwort.";
            }
            
            $('#prompt-output').val(prompt);
            showStep(6);
        };
        
        window.copyPrompt = function() {
            $('#prompt-output')[0].select();
            document.execCommand('copy');
            
            var btn = event.target;
            var originalText = btn.innerHTML;
            btn.innerHTML = '✓ Kopiert!';
            btn.style.background = '#28a745';
            
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.style.background = '';
            }, 2000);
        };
        
        window.resetAll = function() {
            if (confirm('Möchtest du einen neuen Prompt erstellen?')) {
                promptData = {
                    requirements: [],
                    aiMask: 'Standard'
                };
                $('.kpb-input-field').val('');
                $('.kpb-option-card').removeClass('selected');
                $('#citation-section').hide();
                $('#anrede-section').hide();
                $('#gender-section').hide();
                $('#citation-info').hide();
                $('#gender-info').hide();
                $('#seo-section').hide();
                showStep(1);
            }
        };
        
        // Step-Klick Navigation
        $('.kpb-step').on('click', function() {
            var step = $(this).data('step');
            if (step <= currentStep || step === currentStep + 1) {
                showStep(step);
            }
        });
        
        // Init
        updateProgress();
    });
})();
</script>

        <?php
        return ob_get_clean();
    }
}

new KI_Prompt_Builder_Enhanced();
            