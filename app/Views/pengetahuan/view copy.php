<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>

<style>
    .pdf-viewer-container {
        border: 1px solid #eee;
        border-radius: 5px;
        padding: 10px;
        background: #f9f9f9;
        margin-bottom: 20px;
    }

    .pdf-viewer-container embed {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Chatbot Styles */
    .pdf-chat-container {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        background: #fff;
        margin-top: 30px;
    }

    #chat-history {
        height: 300px;
        overflow-y: auto;
        margin-bottom: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
        border: 1px solid #eee;
    }

    .chat-message {
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 8px;
        max-width: 80%;
    }

    .user-message {
        background: #e3f2fd;
        margin-left: auto;
    }

    .bot-message {
        background: #f1f1f1;
        margin-right: auto;
    }

    .chat-input-group {
        display: flex;
        gap: 10px;
    }

    .source-reference {
        font-size: 0.8em;
        color: #666;
        margin-top: 5px;
        border-left: 3px solid #90caf9;
        padding-left: 8px;
    }

    .loading-spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(0, 0, 0, .1);
        border-radius: 50%;
        border-top-color: #007bff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<div class="container my-4">
    <div class="card-body">
        <div class="container d-flex flex-row mt-3">

            <!-- Kolom PDF Viewer -->
            <div class="col-md-8">
                <h2 class="h5"><?= esc($pengetahuan['judul']); ?></h2>
                <?php
                $pdfPath = WRITEPATH . '../public/assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan'];
                $pdfUrl = base_url('assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan']);
                ?>

                <?php if (!empty($pengetahuan['file_pdf_pengetahuan']) && file_exists($pdfPath)): ?>
                    <div class="pdf-viewer-container">
                        <!-- PDF Viewer -->
                        <embed
                            src="<?= $pdfUrl ?>"
                            type="application/pdf"
                            width="100%"
                            height="400px"
                            style="border: 1px solid #ddd;">

                        <!-- PDF Actions -->
                        <div class="text-center mt-2">
                            <a href="<?= $pdfUrl ?>"
                                class="btn btn-sm btn-outline-primary"
                                target="_blank">
                                <i class="bi bi-eye"></i> Buka PDF di Tab Baru
                            </a>
                            <!-- <button onclick="showIframe()" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i> Tampilkan PDF Alternatif
                            </button> -->
                        </div>

                        <!-- Hidden iframe as alternative viewer -->
                        <iframe
                            src="<?= $pdfUrl ?>"
                            width="100%"
                            height="400px"
                            style="border: 1px solid #ddd; display: none;"
                            id="pdfIframe">
                        </iframe>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <?php if (empty($pengetahuan['file_pdf_pengetahuan'])): ?>
                            File PDF tidak tersedia di database
                        <?php else: ?>
                            File PDF tidak ditemukan di server
                        <?php endif; ?>
                    </div>
                <?php endif; ?>



                <!-- Informasi Dokumen -->
                <div class="text-end mt-2">
                    <strong>Di Posting Oleh:</strong> <?= esc($pengetahuan['user_nama']); ?>
                </div>

                <div class="my-3">
                    <p style="text-align: justify;"><?= esc($pengetahuan['caption_pengetahuan']); ?></p>
                </div>
                <hr>
                <ul class="list-unstyled">
                    <li><strong>Dibuat pada:</strong> <?= $pengetahuan['created_at']; ?></li>
                    <li><strong>Diupdate pada:</strong> <?= $pengetahuan['updated_at']; ?></li>
                </ul>
            </div>

            <div class="col-md-4 mb-3 ms-3 border bg-light rounded p-2">
                <h5 class="text-center mt-1" style="margin-left: 1rem;">Daftar Pengetahuan</h5>
                <hr>
                <div class="p-3" style="height: 800px; overflow-y: auto;">
                    <div class="row g-4">
                        <?php foreach ($pengetahuan_lain as $p): ?>
                            <div class="col-12">
                                <div class="card h-100">
                                    <?php if (!empty($p['thumbnail_pengetahuan'])): ?>
                                        <img src="<?= base_url('/assets/uploads/pengetahuan/' . $p['thumbnail_pengetahuan']); ?>"
                                            class="card-img-top bg-light p-1 border"
                                            alt="<?= esc($p['judul']); ?>"
                                            style="height: 200px; object-fit: contain;">
                                    <?php else: ?>
                                        <img src="<?= base_url('/assets/img/default-thumbnail.png'); ?>"
                                            class="card-img-top bg-light p-1 border"
                                            alt="Default Thumbnail"
                                            style="height: 200px; object-fit: contain;">
                                    <?php endif; ?>

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?= esc($p['judul']); ?></h5>
                                        <hr>
                                        <p class="card-text text-justify">
                                            <?= esc(strlen($p['caption_pengetahuan']) > 150 ? substr($p['caption_pengetahuan'], 0, 150) . '...' : $p['caption_pengetahuan']); ?>
                                        </p>

                                        <div class="mt-auto">
                                            <hr>
                                            <p class="card-text">
                                                <small class="text-muted"><?= date('d M Y', strtotime($p['created_at'])); ?></small>
                                            </p>
                                            <a href="<?= base_url('pengetahuan/view/' . $p['id']); ?>" class="btn btn-sm btn-primary w-100">Lihat Detail</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>


        </div>
        <!-- Komentar -->
        <div class="pdf-chat-container">
            <h4 class="h5 mb-3"><i class="bi bi-robot"></i> Tanya Dokumen Ini</h4>

            <div id="chat-history">
                <div class="chat-message bot-message">
                    <p>Halo! Saya asisten AI yang siap menjawab pertanyaan Anda tentang dokumen <strong><?= esc($pengetahuan['judul']); ?></strong>.</p>
                    <p>Anda bisa menanyakan:</p>
                    <ul>
                        <li>Apa poin utama dokumen ini?</li>
                        <li>Jelaskan bagian tertentu</li>
                        <li>Ringkasan dokumen</li>
                    </ul>
                </div>
            </div>

            <div class="chat-input-group">
                <input type="text"
                    id="pdf-question"
                    class="form-control"
                    placeholder="Ketik pertanyaan tentang dokumen..."
                    aria-label="Pertanyaan tentang dokumen">
                <button id="ask-button" class="btn btn-primary">
                    <span id="ask-text">Tanya</span>
                    <span id="ask-spinner" class="loading-spinner"></span>
                </button>
            </div>
            <small class="text-muted">Sistem akan menganalisis isi dokumen untuk memberikan jawaban</small>
        </div>
        <script>
            // PDF Viewer Toggle
            function showIframe() {
                document.querySelector('embed').style.display = 'none';
                document.getElementById('pdfIframe').style.display = 'block';
            }

            // PDF Chatbot Functionality
            document.addEventListener('DOMContentLoaded', function() {
                const chatHistory = document.getElementById('chat-history');
                const questionInput = document.getElementById('pdf-question');
                const askButton = document.getElementById('ask-button');
                const askText = document.getElementById('ask-text');
                const askSpinner = document.getElementById('ask-spinner');
                const pdfId = <?= $pengetahuan['id'] ?>;

                // Auto-scroll chat to bottom
                function scrollToBottom() {
                    chatHistory.scrollTop = chatHistory.scrollHeight;
                }

                // Add message to chat
                function addMessage(content, isUser = false, isError = false) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `chat-message ${isUser ? 'user-message' : 'bot-message'}`;

                    if (isError) {
                        messageDiv.innerHTML = `<p><i class="bi bi-exclamation-triangle"></i> ${content}</p>`;
                    } else {
                        messageDiv.innerHTML = `<p>${content}</p>`;
                    }

                    chatHistory.appendChild(messageDiv);
                    scrollToBottom();
                }

                // Handle asking question
                // Update fungsi askQuestion
                async function askQuestion() {
                    const question = questionInput.value.trim();
                    if (!question) return;

                    // Add user question to chat
                    addMessage(question, true);
                    questionInput.value = '';
                    askButton.disabled = true;
                    askText.textContent = 'Memproses...';
                    askSpinner.style.display = 'inline-block';

                    try {
                        const response = await fetch('/pengetahuan/ask-pdf', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                            },
                            body: JSON.stringify({
                                pdf_id: <?= $pengetahuan['id'] ?>,
                                question: question
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Format the answer with markdown support
                            let answerHtml = data.answer
                                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Bold
                                .replace(/\*(.*?)\*/g, '<em>$1</em>'); // Italic

                            // Add sources if available
                            if (data.sources && data.sources.length > 0) {
                                answerHtml += `<div class="source-reference">Sumber: `;
                                data.sources.forEach((source, index) => {
                                    if (index > 0) answerHtml += ', ';
                                    answerHtml += `Halaman ${source.page || 'N/A'}`;
                                });
                                answerHtml += `</div>`;
                            }

                            addMessage(answerHtml);
                        } else {
                            addMessage(data.message || 'Maaf, terjadi kesalahan saat memproses pertanyaan', false, true);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        addMessage('Gagal terhubung ke server. Silakan coba lagi nanti.', false, true);
                    } finally {
                        askButton.disabled = false;
                        askText.textContent = 'Tanya';
                        askSpinner.style.display = 'none';
                        questionInput.focus();
                    }
                }

                // Event listeners
                askButton.addEventListener('click', askQuestion);
                questionInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') askQuestion();
                });
            });
        </script>

    </div>
    <!-- <a href="/pengetahuan" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a> -->
</div>


<?= $this->endSection(); ?>