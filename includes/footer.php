<?php
?>
<style>
.aurora-footer * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

.aurora-footer {
    background-color: #131313;
    color: #FFFFFF;
    padding: 32px 24px;
    text-align: center;
    font-family: 'Inter', sans-serif;
    font-size: 0.85rem;
    margin-top: auto;
    border-top: 1px solid #E5D3B3;
}

.aurora-footer p {
    opacity: 0.6;
    line-height: 1.6;
    color: #FFFFFF;
}

.aurora-footer a {
    color: #E5D3B3;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
}

.aurora-footer a:hover {
    color: #D31027;
}
</style>
<footer class="aurora-footer">
    <p>&copy; <?= date('Y') ?> Aurora. Alle rechten voorbehouden. Premium design & development door <a href="#">Aurora Team</a>.</p>
</footer>
