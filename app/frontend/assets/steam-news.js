/**
 * Steam News functionality.
 *
 * Handles the fetching and display of Steam news items.
 *
 * @package HW_Steam_Fetch
 */

/* global hwSteamNewsAjax */

/**
 * Escapes HTML special characters in a string.
 *
 * @since 1.0.0
 * @param {*} unsafe The string to be escaped.
 * @return {string} The escaped string.
 */
function escapeHtml(unsafe) {
    if (typeof unsafe !== 'string') {
        return unsafe;
    }
    return unsafe
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

jQuery(function ($) {

    $.ajax({
        url: hwSteamNewsAjax.ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'hw_fetch_game_news',
            post_id: hwSteamNewsAjax.post_id,
            nonce: hwSteamNewsAjax.nonce,
            feednumber: hwSteamNewsAjax.feednumber,
        },
        success: function (response) {
            const newsList = $('.hw_steam_news_list');
            newsList.empty();

            if (response.success) {
                response.data.forEach(function (news) {
                    const truncatedContents = news.contents.substring(0, 550);
                    const safeLabel = escapeHtml(news.feedlabel);
                    const safeTitle = escapeHtml(news.title);
                    const safeDate = escapeHtml(
                        new Date(news.date * 1000).toLocaleDateString()
                    );
                    const safeContents = escapeHtml(truncatedContents);
                    const safeUrl = escapeHtml(news.url);

                    const newsItem = `
                        <div class="hw_steam_news_item">
                            <div class="hw_steam_news_label">${safeLabel}</div>
                            <h3 class="hw_steam_news_title">${safeTitle}</h3>
                            <p class="hw_steam_news_date">${safeDate}</p>
                            <p class="hw_steam_news_content">${safeContents}...</p>
                            <a class="hw_steam_news_readmore button-style"
                               href="${safeUrl}"
                               target="_blank"
                               rel="noopener noreferrer">
                                ${hwSteamNewsAjax.readMoreText}
                            </a>
                        </div>
                    `;

                    newsList.append(newsItem);
                });
            } else {
                const noNewsMessage = `
                    <p class="hw_steam_no_news">
                        ${hwSteamNewsAjax.noNewsText}
                    </p>
                `;

                newsList.append(noNewsMessage);
            }
        },
        error: function () {
            const errorMessage = `
                <p class="hw_steam_error">
                    ${hwSteamNewsAjax.errorText}
                </p>
            `;

            $('.hw_steam_news_list').append(errorMessage);
        }
    });
});
