const textElement = document.getElementById('Backspace_Effect');
const originalText = textElement.textContent;

function backspaceEffect() {
  const intervalId = setInterval(() => {
    if (textElement.textContent.length > 0) {
      textElement.textContent = textElement.textContent.slice(0, -1);
    } else {
      clearInterval(intervalId);
      // Optionally, restore the original text after the backspace effect
      textElement.textContent = originalText;
    }
  }, 100); // Adjust the interval (100ms) to control the backspacing speed
}

backspaceEffect();