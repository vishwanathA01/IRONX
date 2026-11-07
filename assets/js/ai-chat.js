(() => {
	const form = document.getElementById('aiChatForm');
	const input = document.getElementById('aiMessage');
	const messages = document.getElementById('chatMessages');
	const status = document.getElementById('aiStatus');

	if (!form || !input || !messages) return;

	const submitButton = form.querySelector('button[type="submit"]');
	const csrfToken = form.querySelector('input[name="csrf_token"]')?.value || '';
	const endpoint = form.dataset.endpoint;
	const defaultStatus = status?.textContent || '';
	const history = [];

	function addBubble(role, text) {
		const bubble = document.createElement('div');
		bubble.className = `chat-bubble ${role}`;
		bubble.textContent = text;
		messages.appendChild(bubble);
		messages.scrollTop = messages.scrollHeight;
		return bubble;
	}

	form.addEventListener('submit', async (event) => {
		event.preventDefault();
		const message = input.value.trim();
		if (!message || form.dataset.busy === 'true') return;

		if (!csrfToken || !endpoint) {
			if (status) status.textContent = 'Refresh the page and try again.';
			return;
		}

		form.dataset.busy = 'true';
		if (submitButton) submitButton.disabled = true;
		if (status) status.textContent = 'Sending your question...';
		input.value = '';
		addBubble('user', message);
		const replyBubble = addBubble('bot', 'Thinking...');

		try {
			const response = await fetch(endpoint, {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-Token': csrfToken
				},
				body: JSON.stringify({ message, history })
			});

			let result;
			try {
				result = await response.json();
			} catch {
				throw new Error('The server returned an invalid response.');
			}

			if (!response.ok || result.error) {
				throw new Error(result.error || 'The AI coach could not answer. Please try again.');
			}
			if (typeof result.reply !== 'string' || !result.reply.trim()) {
				throw new Error('The AI coach returned an empty response. Please try again.');
			}

			replyBubble.textContent = result.reply;
			history.push(
				{ role: 'user', text: message },
				{ role: 'model', text: result.reply }
			);
			history.splice(0, Math.max(0, history.length - 8));
			if (status) status.textContent = defaultStatus;
		} catch (error) {
			replyBubble.textContent = error.message || 'The AI coach is temporarily unavailable. Please try again.';
			replyBubble.dataset.state = 'error';
			if (status) status.textContent = 'Your message was not answered. You can try again.';
			input.value = message;
		} finally {
			form.dataset.busy = 'false';
			if (submitButton) submitButton.disabled = false;
			messages.scrollTop = messages.scrollHeight;
		}
	});
})();
