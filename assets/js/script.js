// assets/js/script.js

let cycle = 1;
let interval;
let steps = [];

function startTimer(inhale, hold, exhale) {
    resetTimer();
    steps = [
        { label: "Inhale", duration: inhale },
        { label: "Hold", duration: hold },
        { label: "Exhale", duration: exhale },
    ].filter(step => step.duration > 0);

    let i = 0;

    function runStep() {
        if (i >= steps.length) {
            cycle++;
            i = 0;
        }

        const step = steps[i];
        document.getElementById("state").innerText = `${step.label} (Cycle ${cycle})`;
        interval = setTimeout(() => {
            i++;
            runStep();
        }, step.duration * 1000);
    }

    runStep();
}

function resetTimer() {
    clearTimeout(interval);
    document.getElementById("state").innerText = "Repos";
    cycle = 1;
}

// script.js

let breathText = document.getElementById('respiration-text');
let breathing = false;

function startBreathing() {
    if (breathing) return;
    breathing = true;

    let steps = [
        { text: "Inspirez...", duration: 4000 },
        { text: "Expirez...", duration: 4000 }
    ];

    let current = 0;

    function loop() {
        breathText.textContent = steps[current].text;
        setTimeout(() => {
            current = (current + 1) % steps.length;
            loop();
        }, steps[current].duration);
    }

    loop();
}



    // Validation de formulaire côté client
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            // Validation des champs requis
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });

            // Validation des mots de passe (si présent)
            const password = this.querySelector('input[name="password"]');
            const confirmPassword = this.querySelector('input[name="confirm_password"]');
            
            if (password && confirmPassword && password.value !== confirmPassword.value) {
                isValid = false;
                confirmPassword.classList.add('error');
            }

            if (!isValid) {
                event.preventDefault();
                alert('Veuillez remplir tous les champs requis correctement.');
            }
        });
    });