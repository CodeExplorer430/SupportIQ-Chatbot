function sendMessage() {
    const userInput = document.getElementById('user-input');
    const message = userInput.value;
    if (!message) return;

    const chatBox = document.getElementById('chat-box');
    const userMessage = document.createElement('div');
    userMessage.textContent = message;
    userMessage.className = 'user-message animate__animated animate__fadeInRight';
    chatBox.appendChild(userMessage);
    userInput.value = '';

    fetch('php/openai_chat.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        const botMessage = document.createElement('div');
        botMessage.textContent = data.response;
        botMessage.className = 'bot-message animate__animated animate__fadeInLeft';
        chatBox.appendChild(botMessage);
        chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch(error => console.error('Error:', error));
}
