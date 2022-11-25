<div class="principal">
    <div class="relleno">
        <h3 id="cajaTitulo">Buscar posts</h3>
        <div>
            <form id="filterForm" class="cajaBuscador" onsubmit="return;">
                <div id="textoTitulo" class="lista">
                    <label>
                        <input type="text" name="title" placeholder="Título">
                    </label>
                </div>
                <label for="listaTopics">Tema:  </label>
                <select name="topic" id="listaTopics">
                    <option value="-1">Todos</option>
                    <?php
                    foreach ($topics as $topic) {
                        echo '<option value="' . $topic->getId() . '">' . $topic->getName() . '</option>';
                    }
                    ?>
                </select>
                <label for="sort">Ordenar por: </label>
                <select name="sort" id="sort">
                    <option value="mostRecent" selected>Más recientes</option>
                    <option value="mostViews">Más visitas</option>
                    <option value="leastRecent">Menos recientes</option>
                    <option value="leastViews">Menos visitas</option>
                </select>
                <input type="button" value="Crear Post" class="answerButton" id="crearPost" onclick="location.href='/createPost';">
                <input type="button" value="Buscar" class="answerButton" onclick="searchPosts(this.parentElement)">
            </form></div>
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
                <input type="button" value="Ver mas" id="mas" onclick="morePosts()">
            </div>

        </div>
    </div>
    <div id="postsContainer" class="contenidoLista hidden">
            <div id="contenedorRespuestas">
            </div>
            <div class="contenedorBoton">
                <div class="separador">
                    <button id="mas" onclick="morePosts()">Ver mas</button>
                </div>
                
            </div>
        </div>
</div>