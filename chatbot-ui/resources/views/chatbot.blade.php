<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emotion-Based Chatbot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f7f7f8] min-h-screen">

<div class="flex h-screen">
    <aside class="hidden md:flex w-64 bg-[#202123] text-white flex-col">
        <div class="p-4 border-b border-white/10 text-lg font-semibold">
            Emotion Chatbot
        </div>
        <div class="p-4 text-sm text-gray-300">
            Emotion-Based Entertainment Recommender
        </div>
        <div class="px-4 text-xs text-gray-400">
            Share how you feel and get movie or music suggestions.
        </div>
    </aside>

    <main class="flex-1 flex flex-col">
        <div class="border-b bg-white px-6 py-4 shadow-sm">
            <h1 class="text-lg font-semibold text-gray-800">Emotion-Based Entertainment Recommender</h1>
            <p class="text-sm text-gray-500">Chat with the recommender below.</p>
        </div>

        <div id="messages" class="flex-1 overflow-y-auto px-4 py-6 md:px-10 space-y-6">
            <div class="flex justify-start">
                <div class="flex gap-3 max-w-3xl">
                    <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center text-xs font-bold shrink-0">
                        AI
                    </div>
                    <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-md px-4 py-3 shadow-sm text-gray-800">
                        <div class="font-medium">Hi! I’m your emotion-based recommender.</div>
                        <div class="text-sm text-gray-600 mt-1">
                            Tell me how you feel and I’ll suggest something to watch or listen to.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t bg-white px-4 py-4 md:px-8">
            <div class="max-w-4xl mx-auto flex justify-between items-center mb-2 px-1">
                <div class="text-sm text-gray-500">Model:</div>
                <select id="modelSelect" class="border rounded px-2 py-1 text-sm">
                    <option value="transformer">Transformer</option>
                    <option value="vader">VADER</option>
                </select>
            </div>

            <div class="max-w-4xl mx-auto flex gap-3 items-end">
                <input
                    id="messageInput"
                    type="text"
                    placeholder="Message Emotion Chatbot..."
                    class="flex-1 border border-gray-300 rounded-2xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-black focus:border-black bg-white"
                >
                <button
                    id="sendBtn"
                    class="bg-black hover:bg-gray-800 text-white px-5 py-3 rounded-2xl font-medium transition"
                >
                    Send
                </button>
            </div>
        </div>
    </main>
</div>

<script>
const messages = document.getElementById('messages');
const input = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const modelSelect = document.getElementById('modelSelect');

function scrollToBottom() {
    messages.scrollTop = messages.scrollHeight;
}

function addUserMessage(text) {
    const wrapper = document.createElement('div');
    wrapper.className = 'flex justify-end';

    wrapper.innerHTML = `
        <div style="display:flex; gap:12px; max-width:48rem; flex-direction:row-reverse; align-items:flex-start;">
            <div style="width:32px; height:32px; border-radius:9999px; background:#000; color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0;">
                You
            </div>
            <div style="background:#000; color:#fff; padding:12px 16px; border-radius:16px 16px 4px 16px; box-shadow:0 1px 2px rgba(0,0,0,.08); max-width:640px; word-break:break-word;">
                ${text}
            </div>
        </div>
    `;

    messages.appendChild(wrapper);
    scrollToBottom();
}

function addBotMessage(html) {
    const wrapper = document.createElement('div');
    wrapper.className = 'flex justify-start';

    wrapper.innerHTML = `
        <div class="flex gap-3 max-w-3xl">
            <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center text-xs font-bold shrink-0">
                AI
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-md px-4 py-3 shadow-sm text-gray-800">
                ${html}
            </div>
        </div>
    `;

    messages.appendChild(wrapper);
    scrollToBottom();
}

function addRecommendationCard(r) {
    addBotMessage(`
        <div class="space-y-1">
            <div class="font-semibold text-gray-900">🎯 ${r.title}</div>
            <div class="text-xs uppercase tracking-wide text-black font-semibold">${r.type}</div>
            <div class="text-sm text-gray-600">${r.reason}</div>
        </div>
    `);
}

sendBtn.onclick = async () => {
    const text = input.value.trim();
    if (!text) return;

    addUserMessage(text);
    input.value = '';

    const thinkingWrapper = document.createElement('div');
    thinkingWrapper.className = 'flex justify-start';
    thinkingWrapper.innerHTML = `
        <div class="flex gap-3 max-w-3xl">
            <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center text-xs font-bold shrink-0">
                AI
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-md px-4 py-3 shadow-sm text-gray-500 italic">
                Thinking...
            </div>
        </div>
    `;
    messages.appendChild(thinkingWrapper);
    scrollToBottom();

    try {
        const resp = await fetch('/api/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                message: text,
                method: modelSelect.value
            })
        });

        const raw = await resp.text();
        thinkingWrapper.remove();

        if (!resp.ok) {
            addBotMessage(`<span class="text-red-600 font-medium">Error ${resp.status}:</span><br>${raw}`);
            return;
        }

        const data = JSON.parse(raw);

        addBotMessage(`
            <div class="space-y-2">
                <div class="font-medium text-gray-900">${data.reply}</div>
                <div class="text-sm text-gray-600">
                    <span class="font-semibold">Emotion:</span> ${data.emotion}
                </div>
                <div class="text-sm text-gray-600">
                    <span class="font-semibold">Model:</span> ${data.method}
                </div>
                ${data.confidence ? `
                    <div class="text-sm text-gray-600">
                        <span class="font-semibold">Confidence:</span> ${Number(data.confidence).toFixed(2)}
                    </div>
                ` : ''}
            </div>
        `);

        if (data.recommendations && Array.isArray(data.recommendations)) {
            data.recommendations.forEach(r => addRecommendationCard(r));
        }
    } catch (err) {
        thinkingWrapper.remove();
        addBotMessage(`<span class="text-red-600 font-medium">Fetch failed:</span> ${err.message}`);
    }
};

input.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
        e.preventDefault();
        sendBtn.click();
    }
});
</script>

</body>
</html>