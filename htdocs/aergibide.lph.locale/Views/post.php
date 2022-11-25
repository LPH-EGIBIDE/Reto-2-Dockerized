<div class="main">
    <div class="relleno">
        <div class="cajaPregunta" id="cajaPreguntaSkeleton">
            <div class="conTags">
                <div class="c-skeleton__text"></div>
            </div>
            <div class="conPreguntaTitu">
                <div class="c-skeleton__text c-skeleton__text--full c-skeleton__text--small-height"></div>
            </div>
            <div class="conTextoPre">

                <div class="c-skeleton__text"></div>

            </div>
            <div class="conAdjunto">
                <div class="c-skeleton__text"></div>
            </div>
            <div class="conArchivos">
                <div class="c-skeleton__text"></div>
            </div>
        </div>

    <div id="medio">
        <h3 id="tituRes">Respuestas:</h3><input type="button" value="Añadir" class="anadir" id="anadir" onclick="switchHiddenAnswer()">
        <?php
        if (\Utils\AuthUtils::checkAdminAuth()) {
            echo '<input type="button" class="anadir" value="Cerrar post" onclick="closePost()">';
        }
        ?>

    </div>

    <div id="contenedorRespuestas">
       <div class="cajaRespuesta">
           <div class="conTextoRes">
               <div class="c-skeleton__text skeletonRes"></div>
           </div>
           <div class="conAdjunto2">
               <div class="c-skeleton__text"></div>
           </div>
           <div class="conArchivosRe">
               <div class="c-skeleton__text c-skeleton__text--full c-skeleton__text--small-height"></div>
           </div>
           <div class="contenedorAbajo">
               <div class="conUsuario">
                   <div class="c-skeleton__text c-skeleton__text--small"></div>
               </div>
               <div class="parteDrc">
                   <span class="star"><i class="fa-solid fa-star"></i></span>
                   <span class="up"><i class="fa-solid fa-up"></i></span>
               </div>
           </div>
       </div>
      </div>
    </div>
    <div>
        <input type="button" value="Cargar más" class="anadir" onclick="loadMore()">
    </div>
</div>