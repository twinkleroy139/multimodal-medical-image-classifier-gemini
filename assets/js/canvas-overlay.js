class ScanOverlayEngine {
    constructor(imgElementId, canvasElementId) {
        this.img = document.getElementById(imgElementId);
        this.canvas = document.getElementById(canvasElementId);
        this.ctx = this.canvas ? this.canvas.getContext('2d') : null;
        this.boxes = [];
        this.isVisible = false;
        
        window.addEventListener('resize', () => this.draw());
    }

    setBoxes(boundingData) {
        this.boxes = boundingData || [];
    }

    toggle() {
        this.isVisible = !this.isVisible;
        this.draw();
        return this.isVisible;
    }

    draw() {
        if (!this.canvas || !this.img || !this.ctx) return;

        const width = this.img.clientWidth;
        const height = this.img.clientHeight;

        this.canvas.width = width;
        this.canvas.height = height;

        this.ctx.clearRect(0, 0, width, height);

        if (!this.isVisible || this.boxes.length === 0) return;

        this.boxes.forEach(item => {
            if (!item.box_2d || item.box_2d.length !== 4) return;

            const [ymin, xmin, ymax, xmax] = item.box_2d;

            const realYmin = (ymin / 1000) * height;
            const realXmin = (xmin / 1000) * width;
            const realYmax = (ymax / 1000) * height;
            const realXmax = (xmax / 1000) * width;

            const centerX = (realXmin + realXmax) / 2;
            const centerY = (realYmin + realYmax) / 2;
            const radius = Math.max((realXmax - realXmin), (realYmax - realYmin)) / 2 + 8;

            const color = item.type === 'primary' ? '#f43f5e' : '#f59e0b';
            const fillColor = item.type === 'primary' ? 'rgba(244, 63, 94, 0.2)' : 'rgba(245, 158, 11, 0.2)';

            // Draw target ring
            this.ctx.beginPath();
            this.ctx.arc(centerX, centerY, Math.max(radius, 18), 0, 2 * Math.PI);
            this.ctx.lineWidth = 3;
            this.ctx.strokeStyle = color;
            this.ctx.fillStyle = fillColor;
            this.ctx.fill();
            this.ctx.stroke();

            // Label pill
            if (item.label) {
                this.ctx.font = 'bold 12px "Plus Jakarta Sans", sans-serif';
                const textWidth = this.ctx.measureText(item.label).width;
                this.ctx.fillStyle = color;
                this.ctx.fillRect(centerX - (textWidth / 2) - 6, centerY - radius - 22, textWidth + 12, 18);
                this.ctx.fillStyle = '#ffffff';
                this.ctx.fillText(item.label, centerX - (textWidth / 2), centerY - radius - 8);
            }
        });
    }
}