<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('AI Chatbot - Product Manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div id="chat-messages" class="space-y-4 mb-4 h-96 overflow-y-auto border rounded-lg p-4 bg-gray-50">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">AI</div>
                            <div class="bg-white rounded-lg p-3 shadow-sm max-w-[80%]">
                                Hello! I can help you manage your products. Try saying:
                                <ul class="list-disc ml-4 mt-1 text-sm text-gray-600">
                                    <li>"Show all products"</li>
                                    <li>"Add a product called chair for $50"</li>
                                    <li>"Update product 1 price to 100"</li>
                                    <li>"Delete product 1"</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <form id="chat-form" class="flex gap-2">
                        <input type="text" id="message-input"
                            class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Type your message..." required>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                            <span id="send-text">Send</span>
                            <svg id="send-spinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </button>
                    </form>

                    <button id="clear-btn" class="mt-2 text-sm text-gray-500 hover:text-gray-700 underline">Clear conversation</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('chat-form');
            const input = document.getElementById('message-input');
            const messages = document.getElementById('chat-messages');
            const sendText = document.getElementById('send-text');
            const spinner = document.getElementById('send-spinner');
            const clearBtn = document.getElementById('clear-btn');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const message = input.value.trim();
                if (!message) return;

                addMessage('user', message);
                input.value = '';
                setLoading(true);

                try {
                    const res = await axios.post('{{ route("chat.send") }}', { message });
                    addMessage('ai', res.data.reply);
                    if (res.data.action === 'list' || res.data.action === 'create' || res.data.action === 'update' || res.data.action === 'delete') {
                        loadProductTable();
                    }
                } catch (err) {
                    addMessage('ai', 'Sorry, something went wrong. Please try again.');
                } finally {
                    setLoading(false);
                }
            });

            clearBtn.addEventListener('click', async function() {
                messages.innerHTML = '';
                await axios.post('{{ route("chat.clear") }}');
                addMessage('ai', 'Conversation cleared. How can I help you?');
            });

            function addMessage(role, text) {
                const div = document.createElement('div');
                div.className = 'flex items-start gap-3 ' + (role === 'user' ? 'justify-end' : '');
                div.innerHTML = role === 'user'
                    ? `<div class="bg-indigo-600 text-white rounded-lg p-3 shadow-sm max-w-[80%]">${escapeHtml(text)}</div>
                       <div class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">U</div>`
                    : `<div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">AI</div>
                       <div class="bg-white rounded-lg p-3 shadow-sm max-w-[80%]">${escapeHtml(text)}</div>`;
                messages.appendChild(div);
                messages.scrollTop = messages.scrollHeight;
            }

            function setLoading(loading) {
                sendText.classList.toggle('hidden', loading);
                spinner.classList.toggle('hidden', !loading);
                input.disabled = loading;
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML.replace(/\n/g, '<br>');
            }

            async function loadProductTable() {
                try {
                    const res = await axios.get('{{ route("products.index") }}?partial=1');
                } catch(e) {}
            }
        });
    </script>
    @endpush
</x-app-layout>
