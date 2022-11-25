<div class="main">
    <div class="relleno">
        <div class="cambiaContrasena">
            <div class="avtarContra" onclick="changeAvatar()">
                <img src="/api/attachments/id/<?= $user->getAvatar()->getId() ?>" id="avatar" alt="img avatar">
                <p>Haz click sobre la imagen para editarla</p>
            </div>
            <div class="conCambiaContra">
                <form id="changePassword">

                    <p>Nueva contraseña:</p>
                    <input type="password" name="newPassword" class="nuevaContrasena">
                    <p>Contraseña antigua:</p>
                    <input type="password" name="oldPassword" class="viejaContrasena">
                    <div class="conBoton">
                        <input type="submit" value="Cambiar" class="cambiarContraBoton">
                    </div>

                </form>

                <?php
                if ($user->getMfaType() != 0){
                    echo "<button value=\"Cambiar\" class=\"cambiarContraBoton\" onclick=\"disable2Fa();\">Deshabilitar verificacion en 2 pasos</button>";
                } else {
                    echo "<button value=\"Cambiar\" class=\"cambiarContraBoton\" onclick=\"enable2Fa();\">Habilitar verificacion en 2 pasos</button>";
                }
                ?>

            </div>
            <form class="conDescripcion" id="changeDescription">
                <p>Descripción</p>
                <label>
                    <textarea rows="10" class="textAreaDes" name="description"><?= $user->getProfileDescription() ?></textarea>
                </label>
                <div class="conBoton">
                    <input type="submit" value="Editar" class="eviarDescrip">
                </div>
                
            </form>
        </div>
    </div>
</div>	