# BOBO — Reaction Host Character (VEO package)

Формат: шортс с 5–10 смешными моментами, персонаж-«судья» появляется между
моментами / в углу экрана и реагирует: "NEXT!", "Wonderful!", "Great job!" и т.д.

## Персонаж: дедушка Бобо (реальный человек, фотореализм)

Настоящий колоритный дед, как будто снятый на камеру: невысокий полный дедушка
лет 70, большая седая борода, густые выразительные брови, крупный нос, глубокие
морщины-смешинки, круглые очки, тюбетейка и полосатый чапан. Сидит за маленьким
деревянным столом с чайником и пиалой. Смешной с первого взгляда: очень
серьёзный «официальный судья» по виду — и абсолютно детские, преувеличенные
реакции.

Почему именно он: канал называется Bobo Jon («бобо» = дедушка) — персонаж
становится лицом бренда, а не случайной маской.

---

## БАЗОВЫЙ БЛОК (вставлять В КАЖДЫЙ промпт слово в слово, без изменений)

```
Photorealistic cinematic video, real live-action footage, shot on a
professional camera, sharp focus, natural skin texture. A real elderly Central
Asian man in his 70s, short and plump, with a big bushy grey beard, thick
expressive grey eyebrows, a large nose, deep laugh wrinkles, warm brown eyes
behind round glasses. He wears a black-and-white embroidered tubeteika skullcap
and a blue-and-gold striped chapan robe. He sits behind a small wooden desk
with a teapot and a small tea bowl on it. Solid bright green background, flat
evenly lit chroma key green screen. Medium shot, character centered, facing
the camera. No text, no subtitles, no captions, no watermark.
```

---

## 10 РЕАКЦИЙ (базовый блок + этот текст)

**1. NEXT (скучно):**
```
Bobo looks completely unimpressed, half-closed eyes, slowly yawns, then lazily
waves his hand dismissively toward the camera and says in a grumpy elderly
voice: "NEXT!" His huge eyebrows droop with boredom.
```

**2. WONDERFUL (восторг):**
```
Bobo's eyes suddenly go huge behind his glasses, his eyebrows shoot up, he
claps his hands rapidly like an excited child and shouts joyfully: "Wonderful!
WONDERFUL!" His beard bounces as he bounces in his chair.
```

**3. GREAT JOB (одобрение):**
```
Bobo nods slowly with deep respect, strokes his huge beard once, then gives a
big thumbs up to the camera, winks, and says warmly: "Great job, my friend!"
```

**4. Истерический смех:**
```
Bobo bursts into uncontrollable laughter, slaps the desk repeatedly with his
palm, throws his head back, his glasses go crooked, his whole beard shakes, he
wheezes and wipes a tear from his eye. No speech, only wild elderly laughter.
```

**5. ШОК (чай изо рта):**
```
Bobo is calmly sipping tea from his small tea bowl, then suddenly his eyes
bulge, he spits the tea out in a comic spray, coughs, grabs his chest
theatrically, and yells in disbelief: "NO WAY!"
```

**6. Фейспалм (разочарование):**
```
Bobo slowly covers his entire face with his palm, shakes his head, drags the
hand down his face stretching his cheeks comically, and groans: "Oh no no
no..." His eyebrows sag in deep disappointment.
```

**7. Испуг (прячется):**
```
Bobo suddenly screams in comic fright, ducks and hides under the desk so only
his tubeteika and eyes peek over the edge of the desk, trembling. Then he
slowly rises back up, adjusting his glasses, embarrassed.
```

**8. 10 из 10:**
```
Bobo stands up from his chair, raises both thumbs high above his head and
proudly announces like a sports judge: "TEN out of TEN!" Then he bows slightly
with his hand on his chest.
```

**9. Недоумение:**
```
Bobo squints hard, leans very close to the camera so his big nose almost
touches the lens, tilts his head sideways like a confused dog, scratches his
beard and mutters: "What... what is this?"
```

**10. Финал (танец + подписка):**
```
Bobo jumps up, does a funny little victory dance behind the desk, arms waving,
beard swinging side to side, then points directly at the camera with both
hands and shouts happily: "Subscribe, my friend!"
```

---

## Как делать в VEO (Google Flow / Gemini)

1. **Сначала референс-фото.** Сгенерируй ОДНО идеальное фотореалистичное
   изображение Бобо (Imagen / Whisk, промпт = базовый блок, только замени
   "video, footage" на "photo"). Дальше в Flow используй режим **Ingredients to
   Video / Frames to Video** с этим фото — для реального человека это ещё
   критичнее, чем для мультяшки: по одному тексту VEO каждый раз генерирует
   другое лицо, и зритель это сразу заметит. Одно референс-фото = одно лицо во
   всех клипах навсегда.
2. **Зелёный фон обязателен** — клипы кеишь (chroma key) в CapCut и сажаешь
   Бобо кружочком/в угол поверх фейл-видео. Один клип = многоразовый ассет.
3. **Голос в VEO плавает между генерациями.** Реплики держи короткими (1–3
   слова) — разница голоса менее заметна. Надёжный вариант: брать от VEO
   только картинку+смех/звуки, а короткие фразы озвучивать одним своим
   «дедовским» голосом или ElevenLabs (один voice ID навсегда).
4. Клип VEO = 8 сек, в шортсе нужно 1.5–3 сек реакции — обрезай лучший кусок.
5. **Генерируешь библиотеку ОДИН раз** — 10 реакций хватает на десятки видео,
   решается и проблема reused content: поверх чужих фейлов всегда твой
   уникальный персонаж и монтаж.

## Сборка шортса

- Структура: фейл №1 (2–4 сек) → врезка/наплыв Бобо с реакцией (1.5–2 сек) →
  фейл №2 → Бобо → ... → финал с танцем и "Subscribe!"
- Бобо либо на весь экран между моментами (смешнее, его видно), либо кружочком
  в углу поверх видео (динамичнее). Можно чередовать.
- Названия: "Grandpa Bobo Rates Fails 😂 #1", "Try Not To Laugh with Grandpa
  Bobo". Максимум 3 хэштега: #funny #fails #shorts
- Реплики Бобо = будущие мемы канала. Не меняй их формулировки от видео к
  видео — "NEXT!" и "Wonderful!" должны стать узнаваемыми.
