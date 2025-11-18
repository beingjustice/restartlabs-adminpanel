            </div>
        </main>
    </div>
    
    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        updateTime();
        setInterval(updateTime, 60000);
    </script>
</body>
</html>

