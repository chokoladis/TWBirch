class GameApp {
    constructor() {
        this.apiBase = '/api';
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        if (document.getElementById('gameScreen')) {
            this.loadGameState();
        }
    }
    
    setupEventListeners() {
        // Обработка авторизации через Telegram
        window.onTelegramAuth = (user) => this.handleTelegramAuth(user);
        
        // Клики по клеткам
        $('.cell').on('click', (e) => this.handleCellClick(e));
        
        // Кнопки сброса
        $('#resetBtn, #playAgainBtn, #playAgainLoseBtn').on('click', () => this.resetGame());
        
        // Кнопка выхода
        $('#logoutBtn').on('click', () => this.handleLogout());
        
        // Закрытие модальных окон
        $('.close-modal').on('click', function() {
            $(this).closest('.modal').fadeOut(300);
        });
    }
    
    async handleTelegramAuth(user) {
        try {
            const response = await this.apiCall('/auth/login', 'POST', user);
            if (response.success) {
                location.reload();
            } else {
                alert('Ошибка авторизации: ' + (response.error || 'Неизвестная ошибка'));
            }
        } catch (error) {
            console.error('Auth error:', error);
            alert('Ошибка при авторизации');
        }
    }
    
    async handleLogout() {
        if (!confirm('Вы уверены, что хотите выйти?')) {
            return;
        }
        
        try {
            const response = await this.apiCall('/auth/logout', 'POST');
            if (response.success) {
                location.reload();
            } else {
                alert('Ошибка при выходе: ' + (response.error || 'Неизвестная ошибка'));
            }
        } catch (error) {
            console.error('Logout error:', error);
            alert('Ошибка при выходе');
        }
    }
    
    async handleCellClick(e) {
        const $cell = $(e.currentTarget);
        const position = parseInt($cell.data('index'));
        
        if ($cell.hasClass('disabled') || $cell.text() !== '') {
            return;
        }
        
        try {
            const response = await this.apiCall('/game/move', 'POST', { position });
            
            if (!response.success) {
                alert(response.error || 'Ошибка при выполнении хода');
                return;
            }
            
            const { game, computer_position, promo_code } = response.data;
            
            // Обновляем доску
            this.updateBoard(game.board);
            this.updateScores(game.player_score, game.computer_score);
            
            // Если компьютер сделал ход, показываем его
            if (computer_position !== null && computer_position !== undefined) {
                setTimeout(() => {
                    this.updateBoard(game.board);
                }, 300);
            }
            
            // Обработка окончания игры
            if (game.is_finished) {
                this.handleGameEnd(game.winner, promo_code);
            } else {
                this.updateStatus('Ваш ход! Вы играете за ❌');
            }
            
        } catch (error) {
            console.error('Move error:', error);
            alert('Ошибка при выполнении хода');
        }
    }
    
    async loadGameState() {
        try {
            const response = await this.apiCall('/game/state', 'GET');
            
            if (response.success && response.data) {
                const game = response.data.game;
                this.updateBoard(game.board);
                this.updateScores(game.player_score, game.computer_score);
                
                if (game.is_finished) {
                    this.handleGameEnd(game.winner);
                } else {
                    const status = game.current_player === 'X' 
                        ? 'Ваш ход! Вы играете за ❌' 
                        : 'Ход компьютера...';
                    this.updateStatus(status);
                }
            }
        } catch (error) {
            console.error('Load state error:', error);
        }
    }
    
    async resetGame() {
        try {
            const response = await this.apiCall('/game/reset', 'POST');
            
            if (response.success) {
                const game = response.data.game;
                this.updateBoard(game.board);
                this.updateScores(game.player_score, game.computer_score);
                this.updateStatus('Ваш ход! Вы играете за ❌');
                $('.cell').removeClass('disabled');
                $('#resetBtn').hide();
                $('#promoModal').hide();
                $('#loseModal').hide();
            }
        } catch (error) {
            console.error('Reset error:', error);
            alert('Ошибка при сбросе игры');
        }
    }
    
    updateBoard(board) {
        board.forEach((symbol, index) => {
            const $cell = $(`.cell[data-index="${index}"]`);
            $cell.removeClass('x o');
            
            if (symbol === 'X') {
                $cell.addClass('x').html('❌');
            } else if (symbol === 'O') {
                $cell.addClass('o').html('⭕');
            } else {
                $cell.html('');
            }
        });
    }
    
    updateScores(playerScore, computerScore) {
        $('#playerScore').text(playerScore);
        $('#computerScore').text(computerScore);
    }
    
    updateStatus(message) {
        $('#gameStatus').html(`<p>${message}</p>`);
    }
    
    handleGameEnd(winner, promoCode = null) {
        $('.cell').addClass('disabled');
        
        if (winner === 'X') {
            // Игрок выиграл
            if (promoCode) {
                $('#promoCode').text(promoCode);
                $('#promoModal').fadeIn(300);
            }
        } else if (winner === 'O') {
            // Компьютер выиграл
            $('#loseModal').fadeIn(300);
        } else {
            // Ничья
            this.updateStatus('Ничья! Попробуйте ещё раз!');
            $('#resetBtn').show();
        }
    }
    
    async apiCall(endpoint, method = 'GET', data = null) {
        const url = `${this.apiBase}${endpoint}`;
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
        };
        
        if (data && method !== 'GET') {
            options.body = JSON.stringify(data);
        }
        
        const response = await fetch(url, options);
        const result = await response.json();
        
        if (!response.ok && !result.success) {
            throw new Error(result.error || 'API Error');
        }
        
        return result;
    }
}

// Инициализация приложения
$(document).ready(function() {
    new GameApp();
});

