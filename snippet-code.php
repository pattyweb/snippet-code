<div class="scrollable">
    <div class="container breadcrumb glossary" style="padding-bottom: 0px; z-index: 999999; position: relative;">
        @if (function_exists('bcn_display'))
            @php
                bcn_display();
            @endphp
        @endif
    </div>

    <div class="w-layout-vflex search-container" style="background-color:transparent; box-shadow: none; margin-top:-40px">
        <div class="w-layout-vflex search-wrapper">
            <div role="search" id="glossary-search-form">
                <figure data-w-id="cb7543f8-c69a-4742-caa3-fed3b27e064c" style="border-color:rgba(33,37,41,0.24)" class="w-layout-hflex search-input">
                    <div class="search-icon">
                        <img src="<?php echo get_template_directory_uri() . '/resources/images/Left-icon.svg'; ?>" loading="lazy" alt="">
                    </div>
                    <div class="w-layout-hflex search-field">
                        <input type="text" id="glossary-search-input" class="input-placeholder" placeholder="Qual termo você está buscando?" />
                    </div>
                    <div style="display:none" class="close-icon">
                        <img src="<?php echo get_template_directory_uri() . '/resources/images/close-icon.svg'; ?>" loading="lazy" alt="">
                    </div>
                </figure>
            </div>

            <div class="alphabet-wrapper">
                <div class="letter-wrapper">
                    @php
                        $glossario_posts = get_posts(['post_type' => 'glossario', 'numberposts' => -1]);
                        $letters_with_entries = [];
                        foreach ($glossario_posts as $post) {
                            $first_letter = strtoupper(substr($post->post_title, 0, 1));
                            if (preg_match('/[A-Z]/', $first_letter)) {
                                $letters_with_entries[$first_letter] = true;
                            }
                        }
                    @endphp

                    @foreach(range('A', 'Z') as $letter)
                        @php
                            $class = array_key_exists($letter, $letters_with_entries) ? 'active' : 'disabled';
                        @endphp
                        <a href="#letter-{{ $letter }}" data-letter="{{ $letter }}" class="letter-link {{ $class }} w-inline-block letter-{{ strtolower($letter) }}">
                            <div class="letter">{{ $letter }}</div>
                        </a>
                    @endforeach

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var letterLinks = document.querySelectorAll('.letter-link');
                            letterLinks.forEach(function(link) {
                                link.addEventListener('click', function(e) {
                                    letterLinks.forEach(function(l) {
                                        l.classList.remove('selected');
                                    });
                                    link.classList.add('selected');
                                });
                            });
                        });
                    </script>
                </div>
            </div>
        </div>

        <div class="w-layout-vflex content-container alphabet-glossary">
            @php
                $args = [
                    'post_type' => 'glossario',
                    'posts_per_page' => -1,
                    'orderby' => 'title',
                    'order' => 'ASC',
                ];
                $query = new WP_Query($args);
                $entries = [];
                foreach (range('A', 'Z') as $letter) {
                    $entries[$letter] = [];
                }
                $autocomplete_entries = [];
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $title = get_the_title();
                        $descricao = get_field('descricao');
                        $autocomplete_entries[] = ['title' => $title, 'descricao' => $descricao];
                        $first_letter = strtoupper(substr($title, 0, 1));
                        if (array_key_exists($first_letter, $entries)) {
                            $entries[$first_letter][] = [
                                'title' => $title,
                                'descricao' => $descricao
                            ];
                        }
                    }
                    wp_reset_postdata();
                }
                $entries_json = json_encode($autocomplete_entries);
            @endphp

            @foreach (range('A', 'Z') as $letter)
                @if (!empty($entries[$letter]))
                    <div id="letter-{{ $letter }}" class="w-layout-vflex glossary-letter-wrapper" style="cursor: pointer;">
                        <div class="display-96">{{ $letter }}</div>
                        @foreach ($entries[$letter] as $entry)
                            <div data-hover="false" data-delay="0" class="drop-down-list w-dropdown">
                                <div data-w-id="439753fb-620d-5edf-b4ca-7e6a20c9bf26" class="drop-down-toggle w-dropdown-toggle">
                                    <div class="w-layout-hflex drop-heading">
                                        <div class="text-heading-24 top">{{ $letter }}.</div>
                                        <h2 class="text-heading-24 drop">{{ $entry['title'] }}</h2>
                                        <div class="w-layout-vflex drop-btn">
                                            <img src="{{ get_template_directory_uri() }}/resources/images/arrow-drop.svg" loading="lazy" alt="" class="icon-24 arrow-icon">
                                        </div>
                                    </div>
                                </div>
                                <nav class="drop-list light w-dropdown-list" style="display:none;">
                                    <div class="text-body-16 light drop-down">{!! $entry['descricao'] !!}</div>
                                </nav>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
