const canvas = document.getElementById('chart-bg');
if (canvas) {
    const ctx = canvas.getContext('2d');
    let width, height;
    let candles = [];
    const candleWidth = 10;
    const candleSpacing = 5;

    function resize() {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;
        initCandles();
    }

    function initCandles() {
        candles = [];
        let x = 0;
        let price = height / 2;

        while (x < width + 50) {
            const change = (Math.random() - 0.5) * 40;
            const open = price;
            const close = price + change;
            const high = Math.max(open, close) + Math.random() * 20;
            const low = Math.min(open, close) - Math.random() * 20;

            candles.push({ x, open, close, high, low });
            price = close;
            x += candleWidth + candleSpacing;
        }
    }

    function draw() {
        ctx.clearRect(0, 0, width, height);

        // Shift candles left
        candles.forEach(c => c.x -= 0.5);

        // Remove off-screen candles and add new ones
        if (candles[0].x < -20) {
            candles.shift();
            const last = candles[candles.length - 1];
            const change = (Math.random() - 0.5) * 40;
            const open = last.close;
            const close = open + change;
            // Keep within bounds
            const target = height / 2;
            const pull = (target - close) * 0.05; // Pull towards center

            const finalClose = close + pull;

            const high = Math.max(open, finalClose) + Math.random() * 20;
            const low = Math.min(open, finalClose) - Math.random() * 20;

            candles.push({
                x: last.x + candleWidth + candleSpacing,
                open,
                close: finalClose,
                high,
                low
            });
        }

        // Draw candles
        candles.forEach(c => {
            const isGreen = c.close > c.open;
            ctx.fillStyle = isGreen ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)';
            ctx.strokeStyle = isGreen ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)';

            // Wick
            ctx.beginPath();
            ctx.moveTo(c.x + candleWidth / 2, c.high);
            ctx.lineTo(c.x + candleWidth / 2, c.low);
            ctx.stroke();

            // Body
            const bodyHeight = Math.max(1, Math.abs(c.close - c.open));
            const bodyY = Math.min(c.open, c.close);
            ctx.fillRect(c.x, bodyY, candleWidth, bodyHeight);
        });

        requestAnimationFrame(draw);
    }

    window.addEventListener('resize', resize);
    resize();
    draw();
}
