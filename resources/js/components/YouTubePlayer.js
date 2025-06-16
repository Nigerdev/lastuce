/**
 * YouTube Player avec contrôles personnalisés et préchargement
 */
export class YouTubePlayer {
    constructor(element) {
        this.element = element;
        this.videoId = element.dataset.videoId;
        this.autoplay = element.dataset.autoplay === 'true';
        this.suggestedVideos = JSON.parse(element.dataset.suggestedVideos || '[]');
        this.player = null;
        this.isReady = false;
        this.currentTime = 0;
        this.duration = 0;
        this.isPlaying = false;
        
        this.init();
    }

    async init() {
        try {
            await this.loadYouTubeAPI();
            this.createPlayerContainer();
            this.createCustomControls();
            this.initializePlayer();
            this.preloadSuggestedVideos();
        } catch (error) {
            console.error('Erreur lors de l\'initialisation du player YouTube:', error);
            this.showError();
        }
    }

    loadYouTubeAPI() {
        return new Promise((resolve, reject) => {
            if (window.YT && window.YT.Player) {
                resolve();
                return;
            }

            // Charger l'API YouTube
            const script = document.createElement('script');
            script.src = 'https://www.youtube.com/iframe_api';
            script.async = true;
            
            window.onYouTubeIframeAPIReady = () => {
                resolve();
            };

            script.onerror = () => reject(new Error('Impossible de charger l\'API YouTube'));
            document.head.appendChild(script);
        });
    }

    createPlayerContainer() {
        this.element.innerHTML = `
            <div class="youtube-player-container relative bg-black rounded-lg overflow-hidden">
                <div id="youtube-player-${this.videoId}" class="youtube-player-iframe"></div>
                <div class="youtube-player-overlay absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 transition-opacity duration-300">
                    <button class="play-button bg-red-600 hover:bg-red-700 text-white rounded-full p-4 transition-colors duration-200">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 5v10l8-5-8-5z"/>
                        </svg>
                    </button>
                </div>
                <div class="youtube-player-loading absolute inset-0 bg-gray-900 flex items-center justify-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600"></div>
                </div>
            </div>
        `;

        this.playerContainer = this.element.querySelector('.youtube-player-container');
        this.overlay = this.element.querySelector('.youtube-player-overlay');
        this.loadingIndicator = this.element.querySelector('.youtube-player-loading');
        this.playButton = this.element.querySelector('.play-button');

        this.bindOverlayEvents();
    }

    createCustomControls() {
        const controlsHTML = `
            <div class="youtube-custom-controls absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4 opacity-0 transition-opacity duration-300">
                <div class="flex items-center space-x-4">
                    <button class="control-play-pause text-white hover:text-red-400 transition-colors">
                        <svg class="w-6 h-6 play-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 5v10l8-5-8-5z"/>
                        </svg>
                        <svg class="w-6 h-6 pause-icon hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 4h3v12H5V4zm7 0h3v12h-3V4z"/>
                        </svg>
                    </button>
                    
                    <div class="flex-1 flex items-center space-x-2">
                        <span class="current-time text-white text-sm">0:00</span>
                        <div class="progress-container flex-1 relative">
                            <div class="progress-bar bg-gray-600 h-1 rounded-full cursor-pointer">
                                <div class="progress-fill bg-red-600 h-full rounded-full transition-all duration-150" style="width: 0%"></div>
                            </div>
                        </div>
                        <span class="duration text-white text-sm">0:00</span>
                    </div>
                    
                    <button class="control-volume text-white hover:text-red-400 transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.617.824L4.5 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.5l3.883-3.824a1 1 0 011.617.824zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.983 5.983 0 01-1.757 4.243 1 1 0 01-1.415-1.414A3.983 3.983 0 0013 10a3.983 3.983 0 00-1.172-2.829 1 1 0 010-1.414z"/>
                        </svg>
                    </button>
                    
                    <button class="control-fullscreen text-white hover:text-red-400 transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zM16 4a1 1 0 00-1-1h-4a1 1 0 100 2h1.586l-2.293 2.293a1 1 0 001.414 1.414L15 6.414V8a1 1 0 002 0V4zM4 15a1 1 0 001 1h4a1 1 0 000-2H6.414l2.293-2.293a1 1 0 00-1.414-1.414L5 12.586V11a1 1 0 00-2 0v4zM16 15a1 1 0 00-1 1h-4a1 1 0 000-2h1.586l-2.293-2.293a1 1 0 001.414-1.414L15 12.586V11a1 1 0 002 0v4z"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        this.playerContainer.insertAdjacentHTML('beforeend', controlsHTML);
        this.customControls = this.element.querySelector('.youtube-custom-controls');
        this.bindControlEvents();
    }

    initializePlayer() {
        this.player = new YT.Player(`youtube-player-${this.videoId}`, {
            height: '100%',
            width: '100%',
            videoId: this.videoId,
            playerVars: {
                autoplay: this.autoplay ? 1 : 0,
                controls: 0,
                showinfo: 0,
                rel: 0,
                modestbranding: 1,
                playsinline: 1,
            },
            events: {
                onReady: (event) => this.onPlayerReady(event),
                onStateChange: (event) => this.onPlayerStateChange(event),
                onError: (event) => this.onPlayerError(event),
            },
        });
    }

    onPlayerReady(event) {
        this.isReady = true;
        this.duration = this.player.getDuration();
        this.updateDurationDisplay();
        this.hideLoading();
        this.startProgressUpdate();
        
        // Événement personnalisé
        this.element.dispatchEvent(new CustomEvent('youtube:ready', {
            detail: { player: this.player }
        }));
    }

    onPlayerStateChange(event) {
        const state = event.data;
        
        switch (state) {
            case YT.PlayerState.PLAYING:
                this.isPlaying = true;
                this.updatePlayPauseButton();
                this.hideOverlay();
                break;
            case YT.PlayerState.PAUSED:
                this.isPlaying = false;
                this.updatePlayPauseButton();
                this.showOverlay();
                break;
            case YT.PlayerState.ENDED:
                this.isPlaying = false;
                this.updatePlayPauseButton();
                this.showOverlay();
                this.playNextSuggested();
                break;
        }

        // Événement personnalisé
        this.element.dispatchEvent(new CustomEvent('youtube:statechange', {
            detail: { state, player: this.player }
        }));
    }

    onPlayerError(event) {
        console.error('Erreur du player YouTube:', event.data);
        this.showError();
    }

    bindOverlayEvents() {
        this.playButton.addEventListener('click', () => {
            if (this.isReady) {
                if (this.isPlaying) {
                    this.player.pauseVideo();
                } else {
                    this.player.playVideo();
                }
            }
        });

        // Afficher/masquer les contrôles au survol
        this.playerContainer.addEventListener('mouseenter', () => {
            this.showControls();
        });

        this.playerContainer.addEventListener('mouseleave', () => {
            this.hideControls();
        });
    }

    bindControlEvents() {
        // Play/Pause
        const playPauseBtn = this.customControls.querySelector('.control-play-pause');
        playPauseBtn.addEventListener('click', () => {
            if (this.isPlaying) {
                this.player.pauseVideo();
            } else {
                this.player.playVideo();
            }
        });

        // Progress bar
        const progressBar = this.customControls.querySelector('.progress-bar');
        progressBar.addEventListener('click', (e) => {
            const rect = progressBar.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            const seekTime = percent * this.duration;
            this.player.seekTo(seekTime);
        });

        // Volume
        const volumeBtn = this.customControls.querySelector('.control-volume');
        volumeBtn.addEventListener('click', () => {
            if (this.player.isMuted()) {
                this.player.unMute();
            } else {
                this.player.mute();
            }
        });

        // Fullscreen
        const fullscreenBtn = this.customControls.querySelector('.control-fullscreen');
        fullscreenBtn.addEventListener('click', () => {
            this.toggleFullscreen();
        });
    }

    startProgressUpdate() {
        setInterval(() => {
            if (this.isReady && this.isPlaying) {
                this.currentTime = this.player.getCurrentTime();
                this.updateProgressBar();
                this.updateCurrentTimeDisplay();
            }
        }, 1000);
    }

    updateProgressBar() {
        const percent = (this.currentTime / this.duration) * 100;
        const progressFill = this.customControls.querySelector('.progress-fill');
        progressFill.style.width = `${percent}%`;
    }

    updateCurrentTimeDisplay() {
        const currentTimeEl = this.customControls.querySelector('.current-time');
        currentTimeEl.textContent = this.formatTime(this.currentTime);
    }

    updateDurationDisplay() {
        const durationEl = this.customControls.querySelector('.duration');
        durationEl.textContent = this.formatTime(this.duration);
    }

    updatePlayPauseButton() {
        const playIcon = this.customControls.querySelector('.play-icon');
        const pauseIcon = this.customControls.querySelector('.pause-icon');
        
        if (this.isPlaying) {
            playIcon.classList.add('hidden');
            pauseIcon.classList.remove('hidden');
        } else {
            playIcon.classList.remove('hidden');
            pauseIcon.classList.add('hidden');
        }
    }

    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    }

    showOverlay() {
        this.overlay.classList.remove('opacity-0');
        this.overlay.classList.add('opacity-100');
    }

    hideOverlay() {
        this.overlay.classList.add('opacity-0');
        this.overlay.classList.remove('opacity-100');
    }

    showControls() {
        this.customControls.classList.remove('opacity-0');
        this.customControls.classList.add('opacity-100');
    }

    hideControls() {
        if (!this.isPlaying) return;
        this.customControls.classList.add('opacity-0');
        this.customControls.classList.remove('opacity-100');
    }

    hideLoading() {
        this.loadingIndicator.classList.add('hidden');
    }

    showError() {
        this.element.innerHTML = `
            <div class="youtube-player-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <p>Erreur lors du chargement de la vidéo YouTube.</p>
                <button class="retry-button mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Réessayer
                </button>
            </div>
        `;

        const retryButton = this.element.querySelector('.retry-button');
        retryButton.addEventListener('click', () => {
            this.init();
        });
    }

    toggleFullscreen() {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            this.playerContainer.requestFullscreen();
        }
    }

    async preloadSuggestedVideos() {
        if (this.suggestedVideos.length === 0) return;

        try {
            // Précharger les miniatures des vidéos suggérées
            this.suggestedVideos.forEach(videoId => {
                const img = new Image();
                img.src = `https://img.youtube.com/vi/${videoId}/mqdefault.jpg`;
            });
        } catch (error) {
            console.warn('Erreur lors du préchargement des vidéos suggérées:', error);
        }
    }

    playNextSuggested() {
        if (this.suggestedVideos.length > 0) {
            const nextVideoId = this.suggestedVideos[0];
            this.loadVideo(nextVideoId);
        }
    }

    loadVideo(videoId) {
        if (this.isReady) {
            this.videoId = videoId;
            this.player.loadVideoById(videoId);
        }
    }

    // API publique
    play() {
        if (this.isReady) this.player.playVideo();
    }

    pause() {
        if (this.isReady) this.player.pauseVideo();
    }

    stop() {
        if (this.isReady) this.player.stopVideo();
    }

    seekTo(seconds) {
        if (this.isReady) this.player.seekTo(seconds);
    }

    setVolume(volume) {
        if (this.isReady) this.player.setVolume(volume);
    }

    destroy() {
        if (this.player) {
            this.player.destroy();
        }
    }
} 