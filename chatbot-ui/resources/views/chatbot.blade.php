<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Emotion-Based Chatbot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="max-w-2xl mx-auto mt-10 bg-white shadow rounded-lg flex flex-col h-[80vh]">
    <div class="p-4 border-b font-semibold text-lg">
        🎬 Emotion-Based Entertainment Recommender
    </div>

    <div id="messages" class="flex-1 p-4 space-y-3 overflow-y-auto">
        <div class="text-gray-500 text-sm">
            Tell me how you’re feeling, and I’ll suggest something to watch or listen to.
        </div>
    </div>

    <div class="p-4 border-t flex gap-2">
        <input
            id="messageInput"
            type="text"
            placeholder="I feel stressed today..."
            class="flex-1 border rounded px-3 py-2 focus:outline-none focus:ring"
        >
        <button
            id="sendBtn"
            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
        >
            Send
        </button>
    </div>
</div>

<script>
const messages = document.getElementById('messages');
const input = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');

function addMessage(text, isUser = false) {
    const div = document.createElement('div');
    div.className = isUser
        ? 'text-right text-sm'
        : 'text-left text-sm text-gray-800';

    div.innerHTML = isUser
        ? `<span class="inline-block bg-indigo-100 px-3 py-2 rounded">${text}</span>`
        : `<span class="inline-block bg-gray-100 px-3 py-2 rounded">${text}</span>`;

    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
}

sendBtn.onclick = async () => {
    const text = input.value.trim();
    if (!text) return;

    addMessage(text, true);
    input.value = '';

    addMessage('Thinking...', false);

    const resp = await fetch('/api/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        },
        body: JSON.stringify({ message: text })
    });

    messages.lastChild.remove();

    if (!resp.ok) {
        addMessage('Sorry, something went wrong.', false);
        return;
    }

    const data = await resp.json();

    addMessage(
        `${data.reply}<br><strong>Emotion:</strong> ${data.emotion}`,
        false
    );

    if (data.recommendations) {
        data.recommendations.forEach(r => {
            addMessage(`🎯 ${r.title} (${r.type})<br><small>${r.reason}</small>`, false);
        });
    }
};

input.addEventListener('keydown', e => {
    if (e.key === 'Enter') sendBtn.click();
});
</script>

</body>
</html>
