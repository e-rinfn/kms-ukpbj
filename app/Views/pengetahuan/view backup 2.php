<?= $this->extend('templates/template'); ?>

<?= $this->section('content'); ?>
<div class="container my-4">
    <div class="card-body">
        <div class="container d-flex flex-row mt-5">
            <!-- Kolom PDF Viewer -->
            <div class="col-8">
                <h2 class="h5 text-center"><?= $pengetahuan['judul']; ?></h2>
                <embed src="<?= base_url('/assets/uploads/pengetahuan/' . $pengetahuan['file_pdf_pengetahuan']); ?>"
                    type="application/pdf" width="100%" height="600px" />

                <!-- Tambahkan Chatbot PDF Parser -->
                <div class="mt-4 border p-3 rounded">
                    <h4 class="h5 mb-3">Tanya tentang Dokumen Ini</h4>
                    <div id="pdf-chat-container" style="height: 200px; overflow-y: scroll; margin-bottom: 10px; border: 1px solid #ddd; padding: 10px;">
                        <div class="chat-message bot-message">
                            <p>Halo! Tanyakan apa saja tentang dokumen <?= $pengetahuan['judul']; ?> ini.</p>
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="text" id="pdf-chat-input" class="form-control" placeholder="Contoh: Apa isi utama dokumen ini?">
                        <button class="btn btn-primary" id="pdf-chat-send">Tanya</button>
                    </div>
                </div>

                <div class="text-end mt-2">
                    <strong>Di Posting Oleh:</strong> <?= $pengetahuan['user_nama']; ?>
                </div>
                <div class="my-3">
                    <p style="text-align: justify;"><?= esc($pengetahuan['caption_pengetahuan']); ?></p>
                </div>
                <hr>
                <ul class="list-unstyled">
                    <li></li>
                    <!-- <li><strong>Akses Publik:</strong> <?= $pengetahuan['akses_publik'] ? 'Ya' : 'Tidak'; ?></li> -->
                    <li><strong>Dibuat pada:</strong> <?= $pengetahuan['created_at']; ?></li>
                    <li><strong>Diupdate pada:</strong> <?= $pengetahuan['updated_at']; ?></li>
                </ul>
                <!-- <div class="row my-3">
                    <h5 class="col-4">File PDF:</h5>
                    <a href="/assets/uploads/pengetahuan/<?= $pengetahuan['file_pdf_pengetahuan']; ?>"
                        class="btn btn-outline-primary col-8"
                        target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> Download PDF
                    </a>
                </div> -->
            </div>

            <div class="col-4 mb-3 ms-3">
                <h5 class="text-center" style="margin-left: 1rem;">Daftar Pengetahuan</h5>
                <div class="p-3" style="height: 800px; overflow-y: auto;">
                    <div class="row g-4">
                        <?php foreach ($pengetahuan_lain as $p): ?>
                            <div class="col-12">
                                <div class="card h-100">
                                    <?php if (!empty($p['thumbnail_pengetahuan'])): ?>
                                        <img src="<?= base_url('/assets/uploads/pengetahuan/' . $p['thumbnail_pengetahuan']); ?>"
                                            class="card-img-top"
                                            alt="<?= esc($p['judul']); ?>"
                                            style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="<?= base_url('/assets/img/default-thumbnail.png'); ?>"
                                            class="card-img-top"
                                            alt="Default Thumbnail"
                                            style="height: 200px; object-fit: cover;">
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



    </div>
    <a href="/pengetahuan" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>
</div>

</div>

<!-- Tambahkan script untuk PDF Chat -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatContainer = document.getElementById('pdf-chat-container');
        const chatInput = document.getElementById('pdf-chat-input');
        const sendButton = document.getElementById('pdf-chat-send');
        const pdfId = <?= $pengetahuan['id']; ?>;

        function appendMessage(message, isUser = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${isUser ? 'user-message' : 'bot-message'}`;
            messageDiv.innerHTML = `<p>${message}</p>`;
            chatContainer.appendChild(messageDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function sendPdfQuery() {
            const question = chatInput.value.trim();
            if (!question) {
                appendMessage("Silakan masukkan pertanyaan", false);
                return;
            }

            appendMessage(question, true);
            chatInput.value = '';
            sendButton.disabled = true;

            fetch('/pengetahuan/ask-pdf', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        pdf_id: pdfId,
                        question: question
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        appendMessage(data.answer);
                    } else {
                        appendMessage("Error: " + data.answer);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    appendMessage("Gagal mengirim pertanyaan. Pastikan koneksi internet stabil.");
                })
                .finally(() => {
                    sendButton.disabled = false;
                });
        }

        sendButton.addEventListener('click', sendPdfQuery);
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendPdfQuery();
            }
        });
    });
</script>

<style>
    .chat-message {
        margin-bottom: 10px;
        padding: 8px 12px;
        border-radius: 5px;
    }

    .user-message {
        background-color: #e3f2fd;
        margin-left: 20%;
        text-align: right;
    }

    .bot-message {
        background-color: #f5f5f5;
        margin-right: 20%;
    }
</style>

<?= $this->endSection(); ?>