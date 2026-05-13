import { computed } from 'vue';

export function useMasking() {
    /**
     * Masks a string, showing only the last N characters.
     * @param {string|number} value - The value to mask
     * @param {number} visibleCount - Number of characters to keep visible at the end
     */
    const maskAccount = (value, visibleCount = 4) => {
        if (!value) return 'N/A';
        
        const str = value.toString();
        if (str.length <= visibleCount) return str;

        const maskedPart = '•••• '.repeat(1); 
        const visiblePart = str.slice(-visibleCount);
        
        return `${maskedPart}${visiblePart}`;
    };

    return {
        maskAccount
    };
}