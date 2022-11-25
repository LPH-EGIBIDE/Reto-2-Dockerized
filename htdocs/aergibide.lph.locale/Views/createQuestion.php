
<div class="main">
    <div class="relleno">
        <div class="cajaRespuesta" id="agregarRespuesta">
                <form class="answerForm" id="createPost">
                    
                    <div class="contenedorArriba">
                    <div class="select">
                    <p class="tagsPre">
                        <label for="tags">Tema:</label><select name="tag" id="tags">
                        <?php
                        foreach ($topics as $topic){
                            $default = $topic->getId() == $lastTopic ? 'selected' : '';
                            echo "<option ". $default ." value='".$topic->getId()."'>".$topic->getName()."</option>";
                        }
                        ?>
                    </select>
                    </p>
                    </div>
                    <div class="contenedorPregunta">
                        <p class="añadirPre">Titulo de la pregunta:</p>
                        <label>
                            <input class="questionText" type="text" name="title" placeholder="Escriba aquí">
                        </label>
                    </div>
                    </div>

                    <div class="contenedorMedio">
                        <p class="añadirDes">Contenido de la pregunta:</p>
                        <label>
                            <textarea name="content" placeholder="Escriba aquí" oninput='this.style.height = ""; this.style.height = this.scrollHeight + "px"'></textarea>
                        </label>
                    </div>
                        
                    <div class="questionButtons">
                        <input class="questionButton" type="submit" value="Crear pregunta">
                        <input class="questionButton" type="reset" placeholder="Borrar">
                    </div>
                </form>
          </div>
        </div>
    </div>