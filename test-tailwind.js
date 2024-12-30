const tailwindcss = require('tailwindcss');
const fs = require('fs');

// Log content directory scan results
const config = require('./tailwind.config.js');
console.log('Scanning directories:', config.content);

// Check if template file exists and is readable
try {
    const templateContent = fs.readFileSync('./templates/base.html.twig', 'utf8');
    console.log('Template file found and readable');
    console.log('Template contains bg-gray-50:', templateContent.includes('bg-gray-50'));
} catch (error) {
    console.error('Error reading template:', error);
}

// Check if input CSS exists and is readable
try {
    const cssContent = fs.readFileSync('./assets/css/app.css', 'utf8');
    console.log('CSS file found and readable');
    console.log('CSS content:', cssContent);
} catch (error) {
    console.error('Error reading CSS:', error);
}