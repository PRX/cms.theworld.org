(function ($) {
  'use strict';

  /**
   * All of the code for admin-facing JavaScript source
   * should reside in this file.
   */

  $(document).ready(function () {

    // -------------------------------------------------------------
    //   Auto term use Dandelion check
    // -------------------------------------------------------------
    $(document).on('click', '.autoterm_use_dandelion', function (e) {
      autoterm_use_dandelion_action();
    });
    autoterm_use_dandelion_action();
    function autoterm_use_dandelion_action() {
      if ($('.autoterm_use_dandelion').length > 0) {
        if ($('.autoterm_use_dandelion').prop("checked")) {
          $('.autoterm_use_dandelion_children').closest('tr').removeClass('st-hide-content');
        } else {
          $('.autoterm_use_dandelion_children').closest('tr').addClass('st-hide-content');
        }
      }
    }

    // -------------------------------------------------------------
    //   Auto term use OpenCalais check
    // -------------------------------------------------------------
    $(document).on('click', '.autoterm_use_opencalais', function (e) {
      autoterm_use_opencalais_action();
    });
    autoterm_use_opencalais_action();
    function autoterm_use_opencalais_action() {
      if ($('.autoterm_use_opencalais').length > 0) {
        if ($('.autoterm_use_opencalais').prop("checked")) {
          $('.autoterm_use_opencalais_children').closest('tr').removeClass('st-hide-content');
        } else {
          $('.autoterm_use_opencalais_children').closest('tr').addClass('st-hide-content');
        }
      }
    }

    // -------------------------------------------------------------
    //   Auto term Regex check
    // -------------------------------------------------------------
    $(document).on('click', '.autoterm_use_regex', function (e) {
      autoterm_use_regex_action();
    });
    autoterm_use_regex_action();
    function autoterm_use_regex_action() {
      if ($('.autoterm_use_regex').length > 0) {
        if ($('.autoterm_use_regex').prop("checked")) {
          $('.terms_regex_code').closest('tr').removeClass('st-hide-content');
        } else {
          $('.terms_regex_code').closest('tr').addClass('st-hide-content');
        }
      }
    }


    // -------------------------------------------------------------
    //   Break auto link exclusion long element name
    // -------------------------------------------------------------
    if ($('.html-exclusions-customs-form .element-name').length) {
      breakLongAutolinkExlusionWords();
    }

    // -------------------------------------------------------------
    //   Prevent enter on autolink exclusion input from submitting form
    // -------------------------------------------------------------
    $(document).on('keydown', '.html-exclusions-customs-form .element-name', function (event) {
      if (event.keyCode === 13) { // Enter key
        event.preventDefault(); // Prevent form submission
        $('.html-exclusions-customs-form .new-element-submit').trigger('click'); // trigger input submit
      }
    });

    // -------------------------------------------------------------
    //   Show autolink custom element exclusion form
    // -------------------------------------------------------------
    $(document).on('click', '.show-autolink-custom-html-exclusions', function (event) {
      event.preventDefault();

      $('.html-exclusions-customs-add').css('display', 'none');

      $('.html-exclusions-customs-form').css('display', '');
    });

    // -------------------------------------------------------------
    //   Add new autolink custom element exclusion
    // -------------------------------------------------------------
    $(document).on('click', '.html-exclusions-customs-form .new-element-submit', function (event) {
      event.preventDefault();
      
      var input_val = $('.html-exclusions-customs-form .element-name').val();

      if (!isEmptyOrSpaces(input_val)) {
        var new_element_html = '';
        new_element_html += '<tr valign="top" class="html-exclusions-customs-row"><th scope="row"><label for="' + input_val + '">' + input_val + '</label></th><td>';
        new_element_html += '<input type="hidden" name="html_exclusion_customs_entry[]" value="' + input_val + '" />';
        new_element_html += '<input type="checkbox" id="' + input_val + '" name="html_exclusion_customs[]" value="' + input_val + '" checked />';
        new_element_html += '<label for="' + input_val + '" ><code>&lt;' + input_val + '&gt; &lt;/' + input_val + '&gt;</code></label> <span class="delete">Delete</span> <br /> </td></tr>';
        $('.html-exclusions-customs-form').before(new_element_html);  

        breakLongAutolinkExlusionWords();
        
        $('.html-exclusions-customs-form .element-name').val('');
        $('.html-exclusions-customs-form').css('display', 'none');
        $('.html-exclusions-customs-add').css('display', '');  
      }
    });

    // -------------------------------------------------------------
    //   Delete autolink custom element exclusion
    // -------------------------------------------------------------
    $(document).on('click', '.html-exclusions-customs-row .delete', function (event) {
      event.preventDefault();
      $(this).closest('.html-exclusions-customs-row').remove();
    });


    // -------------------------------------------------------------
    //   Accept only slug value for new element form
    // -------------------------------------------------------------
    $(document).on('keyup', '.html-exclusions-customs-form .element-name', function (e) {
      
      var value, original_value
      value = original_value = $(this).val()
      if (e.keyCode !== 9 && e.keyCode !== 37 && e.keyCode !== 38 && e.keyCode !== 39 && e.keyCode !== 40) {
        value = value.replace(/ /g, "")
        value = value.toLowerCase()
        value = replaceDiacritics(value)
        value = replaceNonAlphabet(value)
        value = transliterate(value)

        if (value) {
          if (!value.match(/^[a-z0-9_]+$/i)) {
            //value = replaceSpecialCharacters(value)
          }
        }
        if (value !== original_value) {
          $(this).prop('value', value)
        }
      }
    })


    function isEmptyOrSpaces(str) {
      return str === null || str.match(/^ *$/) !== null;
    }



    // Replace diacritic characters with latin characters.
    function replaceDiacritics(s) {
      var diacritics = [
        /[\300-\306]/g, /[\340-\346]/g,  // A, a
        /[\310-\313]/g, /[\350-\353]/g,  // E, e
        /[\314-\317]/g, /[\354-\357]/g,  // I, i
        /[\322-\330]/g, /[\362-\370]/g,  // O, o
        /[\331-\334]/g, /[\371-\374]/g,  // U, u
        /[\321]/g, /[\361]/g, // N, n
        /[\307]/g, /[\347]/g  // C, c
      ]

      var chars = ['A', 'a', 'E', 'e', 'I', 'i', 'O', 'o', 'U', 'u', 'N', 'n', 'C', 'c']

      for (var i = 0; i < diacritics.length; i++) {
        s = s.replace(diacritics[i], chars[i])
      }

      return s
    }

    function replaceNonAlphabet(s) {
      if ('cpt-ui_page_taxopress_manage_post_types' === window.pagenow) {
        s = s.replace(/[^a-z\s-]/gi, '')
      } else {
        s = s.replace(/[^a-z\s]/gi, '')
      }

      return s
    }


    var cyrillic = {
      "Ё": "YO",
      "Й": "I",
      "Ц": "TS",
      "У": "U",
      "К": "K",
      "Е": "E",
      "Н": "N",
      "Г": "G",
      "Ш": "SH",
      "Щ": "SCH",
      "З": "Z",
      "Х": "H",
      "Ъ": "'",
      "ё": "yo",
      "й": "i",
      "ц": "ts",
      "у": "u",
      "к": "k",
      "е": "e",
      "н": "n",
      "г": "g",
      "ш": "sh",
      "щ": "sch",
      "з": "z",
      "х": "h",
      "ъ": "'",
      "Ф": "F",
      "Ы": "I",
      "В": "V",
      "А": "a",
      "П": "P",
      "Р": "R",
      "О": "O",
      "Л": "L",
      "Д": "D",
      "Ж": "ZH",
      "Э": "E",
      "ф": "f",
      "ы": "i",
      "в": "v",
      "а": "a",
      "п": "p",
      "р": "r",
      "о": "o",
      "л": "l",
      "д": "d",
      "ж": "zh",
      "э": "e",
      "Я": "Ya",
      "Ч": "CH",
      "С": "S",
      "М": "M",
      "И": "I",
      "Т": "T",
      "Ь": "'",
      "Б": "B",
      "Ю": "YU",
      "я": "ya",
      "ч": "ch",
      "с": "s",
      "м": "m",
      "и": "i",
      "т": "t",
      "ь": "'",
      "б": "b",
      "ю": "yu"
    }


    function transliterate(word) {
      return word.split('').map(function (char) {
        return cyrillic[char] || char
      }).join("")
    }

    function breakLongWords(element) {
      var  html_element = decodeHTMLEntities(element.innerHTML);

      if (html_element.indexOf('<wbr>') !== -1) {
        return;
      }
      var words = html_element.split(' ');
          
      for (var i = 0; i < words.length; i++) {
        var word = words[i];
        var wrappedWord = '';

        for (var j = 0; j < word.length; j++) {
          wrappedWord += '<wbr>' + word[j];
        }

        words[i] = wrappedWord;
      }

      element.innerHTML = words.join(' ');
    }

    function breakLongAutolinkExlusionWords() {

      var elements_th = document.querySelectorAll('.html-exclusions-customs-row th label');
      var elements_tr = document.querySelectorAll('.html-exclusions-customs-row td label code');
      for (var i = 0; i < elements_th.length; i++) {
        breakLongWords(elements_th[i]);
      }
      for (var i = 0; i < elements_tr.length; i++) {
        breakLongWords(elements_tr[i]);
      }
    }

    function decodeHTMLEntities(text) {
      var entities = [
        ['amp', '&'],
        ['apos', '\''],
        ['lt', '<'],
        ['gt', '>'],
        ['quot', '"']
      ];
    
      for (var i = 0; i < entities.length; i++) {
        text = text.replace(new RegExp('&' + entities[i][0] + ';', 'g'), entities[i][1]);
      }
    
      return text;
    }
    
  
  });

})(jQuery);
