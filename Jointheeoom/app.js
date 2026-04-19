const codeIds = ['c1', 'c2', 'c3', 'c4', 'c5'];
const inputs = codeIds.map(id => document.getElementById(id));

// Code input: auto-focus, backspace, arrows, paste
inputs.forEach((input, i) => {
  input.addEventListener('input', () => {
    const val = input.value.replace(/[^a-zA-Z0-9]/g, '');
    input.value = val.toUpperCase();
    if (val && i < inputs.length - 1) inputs[i + 1].focus();
    input.classList.toggle('filled', !!val);
  });

  input.addEventListener('keydown', (e) => {
    if (e.key === 'Backspace' && !input.value && i > 0) {
      inputs[i - 1].focus();
      inputs[i - 1].value = '';
      inputs[i - 1].classList.remove('filled');
    }
    if (e.key === 'ArrowLeft' && i > 0) inputs[i - 1].focus();
    if (e.key === 'ArrowRight' && i < inputs.length - 1) inputs[i + 1].focus();
  });

  input.addEventListener('paste', (e) => {
    e.preventDefault();
    const text = (e.clipboardData || window.clipboardData)
      .getData('text')
      .replace(/[^a-zA-Z0-9]/g, '')
      .toUpperCase();

    text.split('').slice(0, 5).forEach((ch, idx) => {
      if (inputs[idx]) {
        inputs[idx].value = ch;
        inputs[idx].classList.add('filled');
      }
    });

    const next = Math.min(text.length, 4);
    inputs[next].focus();
  });
});
// Show error message
function showError(msg) {
  const el = document.getElementById('errorMsg');
  el.textContent = msg;
  setTimeout(() => (el.textContent = ''), 3000);
}

// Join room
function joinRoom() {
  const code = inputs.map(i => i.value).join('');
  const name = document.getElementById('nameInput').value.trim();
  const avatar = document.querySelector('.avatar.selected')?.dataset.emoji || '🦊';

  if (code.length < 5) return showError('Vui lòng nhập đủ 5 ký tự mã phòng!');
  if (!name) return showError('Vui lòng nhập tên hiển thị!');

  console.log({ code, name, avatar });
  // TODO: gọi API tham gia phòng
  alert(`Tham gia phòng: ${code}\nTên: ${name}\nAvatar: ${avatar}`);
}

document.getElementById('btnJoin').addEventListener('click', joinRoom);
