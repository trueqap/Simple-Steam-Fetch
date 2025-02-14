
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

jQuery(document).ready(function ($) {

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
            let newsList = $('.hw_steam_news_list');
            newsList.empty();

            if (response.success) {
                response.data.forEach(function (news) {
                    
                    let truncatedContents = news.contents.substring(0, 550);
                    let safeLabel    = escapeHtml(news.feedlabel);
                    let safeTitle    = escapeHtml(news.title);
                    let safeDate     = escapeHtml(
                        new Date(news.date * 1000).toLocaleDateString()
                    );
                    let safeContents = escapeHtml(truncatedContents);
                    let safeUrl      = escapeHtml(news.url);

                    newsList.append(`
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
                    `);
                });
            } else {
                newsList.append(`
                    <p class="hw_steam_no_news">
                        ${hwSteamNewsAjax.noNewsText}
                    </p>
                `);
            }
        },
        error: function () {
            $('.hw_steam_news_list').append(`
                <p class="hw_steam_error">
                    ${hwSteamNewsAjax.errorText}
                </p>
            `);
        }
    });
});
