<!-- Live Chat Widget -->
<div class="chat-widget" id="chat-widget">
    <div class="chat-header">
        <span>Live Support</span>
        <button id="chat-minimize-btn">-</button>
    </div>
    <div class="chat-box" id="chat-box">
        <div class="chat-message bot-message">
            <p>Hi, how can I help you today?</p>
        </div>
    </div>
    <div class="chat-input">
        <input type="text" id="user-input" placeholder="Type a message..." />
        <button id="send-btn">Send</button>
    </div>
</div>

<!-- Chat Widget CSS -->
<style>
    /* Chat Widget Styles */
    .chat-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 300px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        font-family: Arial, sans-serif;
        transition: all 0.3s ease;
    }

    .chat-header {
        background-color: #007bff;
        color: #fff;
        padding: 10px;
        font-size: 1.2em;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .chat-header button {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.5em;
        cursor: pointer;
    }

    .chat-box {
        padding: 10px;
        max-height: 300px;
        overflow-y: auto;
        background-color: #f9f9f9;
        display: block;
    }

    .chat-message {
        margin: 10px 0;
        padding: 8px;
        border-radius: 5px;
        max-width: 80%;
    }

    .bot-message {
        background-color: #e0e0e0;
        text-align: left;
    }

    .user-message {
        background-color: #007bff;
        color: white;
        text-align: right;
        margin-left: auto;
    }

    .chat-input {
        display: flex;
        padding: 10px;
        background-color: #fff;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .chat-input input {
        width: 80%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .chat-input button {
        padding: 8px 12px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    /* Minimized chat widget */
    .chat-widget.minimized .chat-box,
    .chat-widget.minimized .chat-input {
        display: none;
    }

    .chat-widget.minimized .chat-header {
        background-color: #007bff;
        padding: 10px;
        font-size: 1.2em;
        display: flex;
        justify-content: center;
    }

    .chat-widget.minimized .chat-header button {
        font-size: 1.5em;
        color: white;
        cursor: pointer;
    }
</style>

<!-- Live Chat JavaScript -->
<script>
    // Variables for chat functionality
    const chatWidget = document.getElementById('chat-widget');
    const chatMinimizeBtn = document.getElementById('chat-minimize-btn');
    const sendBtn = document.getElementById('send-btn');
    const userInput = document.getElementById('user-input');
    const chatBox = document.getElementById('chat-box');
    
    // Toggle between minimized and expanded chat
    chatMinimizeBtn.addEventListener('click', function() {
        chatWidget.classList.toggle('minimized');
        // Change button text based on state
        if (chatWidget.classList.contains('minimized')) {
            chatMinimizeBtn.innerHTML = '+';
        } else {
            chatMinimizeBtn.innerHTML = '-';
        }
    });

    // Send message function
    sendBtn.addEventListener('click', function() {
        const userMessage = userInput.value.trim();
        if (userMessage !== '') {
            // Display user message in chat box
            const userMessageDiv = document.createElement('div');
            userMessageDiv.classList.add('chat-message', 'user-message');
            userMessageDiv.innerHTML = `<p>${userMessage}</p>`;
            chatBox.appendChild(userMessageDiv);
            userInput.value = '';

            // Scroll chat to bottom
            chatBox.scrollTop = chatBox.scrollHeight;

            // Display bot reply (dummy response)
            setTimeout(() => {
                const botMessageDiv = document.createElement('div');
                botMessageDiv.classList.add('chat-message', 'bot-message');
                botMessageDiv.innerHTML = `<p>Thanks for your message. How can I assist you further?</p>`;
                chatBox.appendChild(botMessageDiv);

                // Scroll chat to bottom
                chatBox.scrollTop = chatBox.scrollHeight;
            }, 1000);
        }
    });

    // Automatically show the chat widget when the page loads
    window.onload = function() {
        chatWidget.style.display = 'flex'; // Show chat on load
    };
</script>


<!-- Footer Section -->
<div class="footer">
    <div class="footer-content">
        <div class="footer-left">
            <h4>Contact Us</h4>
            <p>Phone: +880 123 456 789</p>
            <p>Email: support@bangladeshpolice.gov.bd</p>
            <p>Address: Police HQ, Dhaka, Bangladesh</p>
        </div>

        <div class="footer-center">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms & Conditions</a></li>
                <li><a href="#">FAQ</a></li>
            </ul>
        </div>

        <div class="footer-right">
            <h4>Follow Us</h4>
            <ul>
                <li><a href="#">Facebook</a></li>
                <li><a href="#">Twitter</a></li>
                <li><a href="#">Instagram</a></li>
                <li><a href="#">YouTube</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© 2024 Bangladesh Police Department. All Rights Reserved.</p>
    </div>
</div>

<!-- Style for Footer -->
<style>
    .footer {
        background-color: #2c3e50;
        color: white;
        padding: 30px 0;
        font-family: 'Arial', sans-serif;
    }

    .footer-content {
        display: flex;
        justify-content: space-around;
        max-width: 1200px;
        margin: 0 auto;
    }

    .footer-left, .footer-center, .footer-right {
        width: 30%;
    }

    .footer h4 {
        font-size: 1.3em;
        margin-bottom: 15px;
    }

    .footer ul {
        list-style-type: none;
        padding: 0;
    }

    .footer ul li {
        margin: 5px 0;
    }

    .footer ul li a {
        text-decoration: none;
        color: #ecf0f1;
        transition: color 0.3s;
    }

    .footer ul li a:hover {
        color: #3498db;
    }

    .footer-bottom {
        text-align: center;
        margin-top: 20px;
        font-size: 0.9em;
    }

    .footer-bottom p {
        margin: 10px 0;
    }

    /* Responsive Footer */
    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
            text-align: center;
        }

        .footer-left, .footer-center, .footer-right {
            width: 100%;
            margin-bottom: 20px;
        }
    }
</style>

</body>
</html>
