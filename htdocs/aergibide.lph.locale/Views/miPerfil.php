<div class="main">
    <div class="relleno">
        <div class="cajaPerfil">
            <div class="cajaPerfilUp">
                <input type="hidden" value="<?= htmlspecialchars($profileUser->getId(), ENT_QUOTES, 'UTF-8'); ?>" id="userId">
                <img src="/api/attachments/id/<?=  htmlspecialchars($profileUser->getAvatar()->getId(), ENT_QUOTES, 'UTF-8'); ?>" alt="img Avatar" id="avatar">
                <p class="nombreUsuario"><?=  $profileUser->getUsername(); ?></p>
                <p class="ultPregunta">Ultima pregunta: <span class="fechaMi overflow-1"> <?=htmlspecialchars($userLastPost, ENT_QUOTES, 'UTF-8')  ?></span></p>
                <i class="fa-regular fa-trophy" id="rango"><span class="rank"><?=htmlspecialchars($profileUser->getPoints(), ENT_QUOTES, 'UTF-8')  ?></span></i>
            <div class="stats">
                <i class="fa-solid fa-star" id="star"><span class="Fav"><?=htmlspecialchars($userFavoriteCount, ENT_QUOTES, 'UTF-8')  ?></span></i>
                <i class="fa-solid fa-up" id="up"><span class="upVotw"><?=htmlspecialchars($userUpvoteCount, ENT_QUOTES, 'UTF-8')  ?></span></i>
            </div>
            </div>
            <hr>
            <div class="cajaPerfilDown">
            <p class="descripcionUsuario"><?= htmlspecialchars($profileUser->getProfileDescription(), ENT_QUOTES, 'UTF-8'); ?></p>
            <div class="navPerfil">
                <ul class="navOpciones">
                    <li><a  id="linkPre" class="active" data-tab="posts">Preguntas</a></li>
                    <li><a  id="linkRes" data-tab="answers">Respuestas</a></li>
                    <li><a  id="linkFav" data-tab="favourites">Favoritos</a></li>
                    <?php if (\Utils\AuthUtils::checkAdminAuth()) {
                        if ($profileUser->isActive()) {
                            echo '<li><a onclick="deactivateUser()">Desactivar usuario</a></li>';
                        } else {
                            echo '<li><a onclick="reactivateUser()">Reaactivar usuario</a></li>';
                        }

                    } ?>
                </ul>
            </div>
            </div>
        </div>
        <div id="skeletonContainer" class="contenidoLista">
            <div class="plantillaRespuestas">
                <div class="contenedorLista">
                    <div class="contenedorIden">
                        <div class="c-skeleton__figure c-skeleton__text--bigger identificadoAva"></div>
                    </div>

                    <div class="contenidoIzq">
                        <div class="c-skeleton__text c-skeleton__text--title c-skeleton__text--small-height tituPregunta"></div>
                        <div class="c-skeleton__text c-skeleton__text--bigger "></div>
                        <div class="c-skeleton__text c-skeleton__text--medium"></div>
                    </div>

                </div>
                <hr>
            </div>

            <div class="plantillaRespuestas">
                <div class="contenedorLista">
                    <div class="contenedorIden">
                        <div class="c-skeleton__figure c-skeleton__text--bigger identificadoAva"></div>
                    </div>

                    <div class="contenidoIzq">
                        <div class="c-skeleton__text c-skeleton__text--title c-skeleton__text--small-height tituPregunta"></div>
                        <div class="c-skeleton__text c-skeleton__text--bigger "></div>
                        <div class="c-skeleton__text c-skeleton__text--medium"></div>
                    </div>

                </div>
                <hr>
            </div><div class="plantillaRespuestas">
                <div class="contenedorLista">
                    <div class="contenedorIden">
                        <div class="c-skeleton__figure c-skeleton__text--bigger identificadoAva"></div>
                    </div>

                    <div class="contenidoIzq">
                        <div class="c-skeleton__text c-skeleton__text--title c-skeleton__text--small-height tituPregunta"></div>
                        <div class="c-skeleton__text c-skeleton__text--bigger "></div>
                        <div class="c-skeleton__text c-skeleton__text--medium"></div>
                    </div>

                </div>
                <hr>
            </div><div class="plantillaRespuestas">
                <div class="contenedorLista">
                    <div class="contenedorIden">
                        <div class="c-skeleton__figure c-skeleton__text--bigger identificadoAva"></div>
                    </div>

                    <div class="contenidoIzq">
                        <div class="c-skeleton__text c-skeleton__text--title c-skeleton__text--small-height tituPregunta"></div>
                        <div class="c-skeleton__text c-skeleton__text--bigger "></div>
                        <div class="c-skeleton__text c-skeleton__text--medium"></div>
                    </div>

                </div>
                <hr>
            </div><div class="plantillaRespuestas">
                <div class="contenedorLista">
                    <div class="contenedorIden">
                        <div class="c-skeleton__figure c-skeleton__text--bigger identificadoAva"></div>
                    </div>

                    <div class="contenidoIzq">
                        <div class="c-skeleton__text c-skeleton__text--title c-skeleton__text--small-height tituPregunta"></div>
                        <div class="c-skeleton__text c-skeleton__text--bigger "></div>
                        <div class="c-skeleton__text c-skeleton__text--medium"></div>
                    </div>

                </div>
                <hr>
            </div><div class="contenedorBoton">
                <div class="separador">
                    <input type="button" value="Ver mas" id="mas">
                </div>

            </div>
        </div>
        <div id="postsContainer"  class="contenidoLista hidden">
            <div id="contenedorRespuestas">
            </div>
            <div class="contenedorBoton">
                <div class="separador">
                    <button id="mas" onclick="morePosts()">Ver mas</button>
                </div>
                
            </div>
        </div>
    </div>
</div>	