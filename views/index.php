<?php
use App\Core\Config;
use App\Repositories\UserRepository;

Config::load();
$userRepository = new UserRepository();
$user = $userRepository->getCurrentUser();
$isAuthorized = $user !== null;

$config = Config::get('telegram');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Крестики-нолики</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <?php if (!$isAuthorized): ?>
            <!-- Экран авторизации -->
            <div class="auth-screen" id="authScreen">
                <div class="auth-content">
                    <h1>🌸 Добро пожаловать! 🌸</h1>
                    <p class="auth-subtitle">Войдите через Telegram, чтобы начать игру</p>
                    <div id="telegram-login-container">
                        <script async src="https://telegram.org/js/telegram-widget.js?22" 
                                data-telegram-login="<?= htmlspecialchars($config['bot_username']); ?>" 
                                data-size="large" 
                                data-onauth="onTelegramAuth(user)" 
                                data-request-access="write"></script>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Игровой экран -->
            <div class="game-screen" id="gameScreen">
                <div class="game-header">
                    <div class="header-top">
                        <h1>💖 Крестики-нолики 💖</h1>
                        <button class="btn-logout" id="logoutBtn" title="Выйти">🚪</button>
                    </div>
                    <p class="player-name">Привет, <?php echo htmlspecialchars($user->getFirstName()); ?>!</p>
                </div>
                
                <div class="game-info">
                    <div class="score">
                        <span class="player-score">Вы: <span id="playerScore">0</span></span>
                        <span class="computer-score">Компьютер: <span id="computerScore">0</span></span>
                    </div>
                </div>
                
                <div class="game-board" id="gameBoard">
                    <div class="cell" data-index="0"></div>
                    <div class="cell" data-index="1"></div>
                    <div class="cell" data-index="2"></div>
                    <div class="cell" data-index="3"></div>
                    <div class="cell" data-index="4"></div>
                    <div class="cell" data-index="5"></div>
                    <div class="cell" data-index="6"></div>
                    <div class="cell" data-index="7"></div>
                    <div class="cell" data-index="8"></div>
                </div>
                
                <div class="game-status" id="gameStatus">
                    <p>Ваш ход! Вы играете за ❌</p>
                </div>
                
                <button class="btn-reset" id="resetBtn" style="display: none;">Играть снова</button>
            </div>
            
            <!-- Модальное окно для промокода -->
            <div class="modal" id="promoModal" style="display: none;">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h2>🎉 Поздравляем! Вы выиграли! 🎉</h2>
                    <div class="promo-code-container">
                        <p class="promo-label">Ваш промокод на скидку:</p>
                        <div class="promo-code" id="promoCode"></div>
                    </div>
                    <button class="btn-play-again" id="playAgainBtn">Играть снова</button>
                </div>
            </div>
            
            <!-- Модальное окно для проигрыша -->
            <div class="modal" id="loseModal" style="display: none;">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h2>😔 Вы проиграли</h2>
                    <p>Не расстраивайтесь! Попробуйте ещё раз!</p>
                    <button class="btn-play-again" id="playAgainLoseBtn">Играть снова</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="/assets/js/app.js"></script>
</body>
</html>

