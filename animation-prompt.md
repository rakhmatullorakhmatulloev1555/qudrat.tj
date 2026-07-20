# Universal Image-to-Video Animation Prompt (stickman MS Paint frames)

## MASTER PROMPT (вставлять с каждой картинкой)

2D hand-drawn doodle animation. Keep the exact crude amateur MS Paint stick figure
style of the input image: white background, thick shaky black outlines, flat colors,
no shading, no 3D, no realism, no added details. Minimal smooth cartoon motion, like
a hand-drawn flipbook. The composition, characters and text stay exactly as drawn.
Do not redraw, do not add new objects, do not change colors. Subtle motion only:
[ОПИСАНИЕ ДВИЖЕНИЯ ДЛЯ ЭТОГО КАДРА]. Static camera, no zoom, no pan. 4 seconds.

## NEGATIVE PROMPT (если сервис поддерживает)

realistic, 3D render, shading, gradients, camera movement, zoom, new characters,
morphing faces, extra limbs, text changing or melting, style change, color shift,
detailed background

## Шаблоны движения — подставлять в [ОПИСАНИЕ ДВИЖЕНИЯ]

- Персонаж стоит/говорит: "the stick figure sways gently, blinks, and tilts his head slightly"
- График/стрелка: "the chart line slowly extends upward (or downward), the label pulses once"
- Деньги/монеты: "coins fall steadily and bounce softly, the money bag wobbles"
- Толпа: "the crowd of stick figures sway and nod at slightly different rhythms"
- Падение/крах: "the building (or figure) slowly tips over and falls, dust puffs appear"
- Документы/письма: "papers flutter down one by one, the reading figure's eyes move along lines"
- Эмоция-удар (траур, шок): "almost still frame, only a slow blink and a slight shoulder drop"

## Правила

1. Одно движение на кадр. Чем меньше просишь, тем меньше модель ломает стиль.
2. Камера всегда статичная: stickman-стиль разваливается при зуме и панораме.
3. Длительность 3–5 секунд, дальше модели начинают дорисовывать отсебятину.
4. Если в кадре есть текст ($725M, BANKRUPT и т.п.), добавь в промт:
   "the hand-written text stays perfectly static and unchanged".
5. Если модель перерисовывает стиль в гладкий вектор, добавь в начало:
   "low frame rate animation, 8 frames per second, wobbly hand-drawn lines".
