</main>
    
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <span>La Cacau</span>
                </div>
                
                <div class="footer-links">
                    <div class="footer-section">
                        <h3>Navegação</h3>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="carrinho.php">Carrinho</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-section">
                        <h3>Categorias</h3>
                        <ul>
                            <?php
                           
                            if (!isset($categorias)) {
                                $categorias = query("SELECT * FROM categorias ORDER BY nome LIMIT 5");
                            }
                            
                            foreach ($categorias as $cat): 
                            ?>
                                <li><a href="categoria.php?id=<?php echo $cat['id']; ?>"><?php echo $cat['nome']; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="footer-section">
                        <h3>Contato</h3>
                        <ul>
                            <li>Email: contato@lacacau.com</li>
                            <li>Telefone: (14) 98845-6732</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> La Cacau. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>