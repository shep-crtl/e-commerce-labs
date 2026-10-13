const registerForm = document.getElementById('register-form');

if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
        let valid = true;

        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPasswordElem = document.getElementById('confirm_password');
        const confirmPassword = confirmPasswordElem ? confirmPasswordElem.value : '';
        const country = document.getElementById('country').value;
        const city = document.getElementById('city').value.trim();
        const contact = document.getElementById('contact').value.trim();

        document.querySelectorAll('.field-error').forEach(function (element) {
            element.textContent = '';
        });

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^[0-9+\-\s]{7,15}$/;
        const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

        if (name.length < 2) {
            document.getElementById('name-error').textContent = 'Please enter your full name.';
            valid = false;
        }

        if (!emailRegex.test(email) || email.length > 50) {
            document.getElementById('email-error').textContent = 'Please enter a valid email.';
            valid = false;
        }

        if (!passRegex.test(password)) {
            document.getElementById('password-error').textContent =
                'Password must be at least 8 characters long and contain uppercase, lowercase, number, and special character.';
            valid = false;
        }

        if (confirmPasswordElem && confirmPassword !== password) {
            document.getElementById('confirm-password-error').textContent = 'Passwords do not match.';
            valid = false;
        }

        if (country === '') {
            document.getElementById('country-error').textContent = 'Please select a country.';
            valid = false;
        }

        if (city.length < 2) {
            document.getElementById('city-error').textContent = 'Please enter your city.';
            valid = false;
        }

        if (!phoneRegex.test(contact)) {
            document.getElementById('contact-error').textContent = 'Please enter a valid contact number.';
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
}

// Client-side validation for Product form
const productForm = document.getElementById('product-form');
if (productForm) {
    productForm.addEventListener('submit', function (e) {
        let valid = true;

        const titleElem = document.getElementById('product_title');
        const priceElem = document.getElementById('product_price');
        const catElem = document.getElementById('product_cat');
        const brandElem = document.getElementById('product_brand');
        const imageElem = document.getElementById('product_image');
        const isEditElem = document.getElementById('is_edit');

        document.querySelectorAll('.field-error').forEach(function (el) {
            el.textContent = '';
        });

        if (titleElem && titleElem.value.trim() === '') {
            const errSpan = document.getElementById('product_title-error');
            if (errSpan) errSpan.textContent = 'Product title is required.';
            valid = false;
        }

        if (priceElem && (isNaN(priceElem.value) || parseFloat(priceElem.value) < 0 || priceElem.value.trim() === '')) {
            const errSpan = document.getElementById('product_price-error');
            if (errSpan) errSpan.textContent = 'Please enter a valid positive price.';
            valid = false;
        }

        if (catElem && (catElem.value === '' || catElem.value === '0')) {
            const errSpan = document.getElementById('product_cat-error');
            if (errSpan) errSpan.textContent = 'Please select a category.';
            valid = false;
        }

        if (brandElem && (brandElem.value === '' || brandElem.value === '0')) {
            const errSpan = document.getElementById('product_brand-error');
            if (errSpan) errSpan.textContent = 'Please select a brand.';
            valid = false;
        }

        // Validate image upload on client side if a file is selected (or required for new products)
        if (imageElem && imageElem.files.length > 0) {
            const file = imageElem.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (!allowedTypes.includes(file.type)) {
                const errSpan = document.getElementById('product_image-error');
                if (errSpan) errSpan.textContent = 'Only JPG, PNG, GIF, and WebP images are allowed.';
                valid = false;
            } else if (file.size > maxSize) {
                const errSpan = document.getElementById('product_image-error');
                if (errSpan) errSpan.textContent = 'Image size must not exceed 2MB.';
                valid = false;
            }
        } else if (imageElem && (!isEditElem || isEditElem.value !== '1')) {
            const errSpan = document.getElementById('product_image-error');
            if (errSpan) errSpan.textContent = 'Product image is required.';
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
}