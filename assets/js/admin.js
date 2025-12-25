(function($) {
    'use strict';
    
    let brands = [];
    let selectedBrands = [];
    let currentSessionId = null;
    let processingInterval = null;
    
    /**
     * Initialize
     */
    $(document).ready(function() {
        loadBrands();
        bindEvents();
    });
    
    /**
     * Load brands from server
     */
    function loadBrands() {
        $.ajax({
            url: bptS.ajaxUrl,
            type: 'POST',
            data: {
                action: 'bpt_s_get_brands',
                nonce: bptS.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    brands = response.data;
                    renderBrands();
                } else {
                    $('#bpt-s-brands-list').html('<p class="bpt-s-error">' + (response.data && response.data.message ? response.data.message : 'Failed to load brands.') + '</p>');
                }
            },
            error: function() {
                $('#bpt-s-brands-list').html('<p class="bpt-s-error">Error loading brands. Please refresh the page.</p>');
            }
        });
    }
    
    /**
     * Render brands list
     */
    function renderBrands() {
        if (brands.length === 0) {
            $('#bpt-s-brands-list').html('<p>No brands found.</p>');
            return;
        }
        
        let html = '';
        brands.forEach(function(brand) {
            html += '<div class="bpt-s-brand-item">';
            html += '<label>';
            html += '<input type="checkbox" class="bpt-s-brand-checkbox" value="' + brand.id + '" />';
            html += '<span>' + brand.name + '</span>';
            html += '<span class="bpt-s-brand-count">(' + brand.count + ')</span>';
            html += '</label>';
            html += '</div>';
        });
        
        $('#bpt-s-brands-list').html(html);
    }
    
    /**
     * Bind events
     */
    function bindEvents() {
        // Select all brands
        $(document).on('change', '#bpt-s-select-all-brands', function() {
            const checked = $(this).is(':checked');
            $('.bpt-s-brand-checkbox').prop('checked', checked);
            updateSelectedBrands();
        });
        
        // Individual brand checkbox
        $(document).on('change', '.bpt-s-brand-checkbox', function() {
            updateSelectedBrands();
            updateSelectAllState();
        });
        
        // Submit button
        $('#bpt-s-submit-btn').on('click', function() {
            if ($(this).prop('disabled')) {
                return;
            }
            
            const productType = $('#bpt-s-product-type').val();
            
            if (selectedBrands.length === 0) {
                alert(bptS.strings.noBrandsSelected);
                return;
            }
            
            if (!productType) {
                alert(bptS.strings.noProductTypeSelected);
                return;
            }
            
            startProcessing(selectedBrands, productType);
        });
    }
    
    /**
     * Update selected brands array
     */
    function updateSelectedBrands() {
        selectedBrands = [];
        $('.bpt-s-brand-checkbox:checked').each(function() {
            selectedBrands.push(parseInt($(this).val()));
        });
    }
    
    /**
     * Update select all checkbox state
     */
    function updateSelectAllState() {
        const total = $('.bpt-s-brand-checkbox').length;
        const checked = $('.bpt-s-brand-checkbox:checked').length;
        $('#bpt-s-select-all-brands').prop('checked', total > 0 && total === checked);
    }
    
    /**
     * Start processing
     */
    function startProcessing(brandIds, productType) {
        // Disable form
        $('#bpt-s-submit-btn').prop('disabled', true);
        $('.bpt-s-brand-checkbox, #bpt-s-product-type').prop('disabled', true);
        
        // Reset and show progress section
        $('#bpt-s-logs').empty();
        $('#bpt-s-result-message').hide();
        $('#bpt-s-progress-bar-fill').css('width', '0%');
        $('#bpt-s-progress-percent').text('0%');
        $('#bpt-s-progress-count').text('(0 / 0)');
        $('#bpt-s-stat-success').text('0');
        $('#bpt-s-stat-errors').text('0');
        $('#bpt-s-progress-section').show();
        
        // Initialize processing
        $.ajax({
            url: bptS.ajaxUrl,
            type: 'POST',
            data: {
                action: 'bpt_s_switch_product_types',
                nonce: bptS.nonce,
                brand_ids: brandIds,
                product_type: productType
            },
            success: function(response) {
                if (response.success && response.data) {
                    currentSessionId = response.data.session_id;
                    processBatch(0);
                } else {
                    alert(response.data && response.data.message ? response.data.message : 'Failed to start processing.');
                    resetForm();
                }
            },
            error: function() {
                alert('Error starting processing. Please try again.');
                resetForm();
            }
        });
    }
    
    /**
     * Process batch
     */
    function processBatch(offset) {
        if (!currentSessionId) {
            return;
        }
        
        $.ajax({
            url: bptS.ajaxUrl,
            type: 'POST',
            data: {
                action: 'bpt_s_process_batch',
                nonce: bptS.nonce,
                session_id: currentSessionId,
                offset: offset
            },
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    
                    // Update progress
                    updateProgress(data);
                    
                    // Add logs
                    if (data.logs && data.logs.length > 0) {
                        addLogs(data.logs);
                    }
                    
                    // Check if completed
                    if (data.completed) {
                        completeProcessing(data);
                    } else {
                        // Process next batch with delay
                        setTimeout(function() {
                            processBatch(data.offset || offset + 5);
                        }, 500); // 500ms delay between batches
                    }
                } else {
                    addLog('Error: ' + (response.data && response.data.message ? response.data.message : 'Unknown error'), 'error');
                    resetForm();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error, xhr.responseText);
                addLog('Error processing batch: ' + (error || status || 'Unknown error'), 'error');
                resetForm();
            }
        });
    }
    
    /**
     * Update progress bar and stats
     */
    function updateProgress(data) {
        const progress = data.progress || 0;
        const processed = data.processed || 0;
        const total = data.total || 0;
        const success = data.success || 0;
        const skipped = data.skipped || 0;
        const errors = data.errors || 0;
        
        // Update progress bar
        $('#bpt-s-progress-bar-fill').css('width', progress + '%');
        $('#bpt-s-progress-percent').text(progress + '%');
        $('#bpt-s-progress-count').text('(' + processed + ' / ' + total + ')');
        
        // Update stats - show actual changes (success - skipped)
        $('#bpt-s-stat-success').text(success);
        $('#bpt-s-stat-errors').text(errors);
    }
    
    /**
     * Add logs
     */
    function addLogs(logs) {
        logs.forEach(function(log) {
            let className = 'info';
            if (log.toLowerCase().indexOf('error') !== -1 || log.toLowerCase().indexOf('failed') !== -1) {
                className = 'error';
            } else if (log.toLowerCase().indexOf('skipped') !== -1) {
                className = 'skipped';
            } else if (log.toLowerCase().indexOf('success') !== -1) {
                className = 'success';
            }
            addLog(log, className);
        });
    }
    
    /**
     * Add single log entry
     */
    function addLog(message, className) {
        const logEntry = $('<div class="bpt-s-log-entry ' + (className || 'info') + '">' + escapeHtml(message) + '</div>');
        $('#bpt-s-logs').append(logEntry);
        
        // Auto scroll to bottom
        const logsContainer = $('#bpt-s-logs');
        logsContainer.scrollTop(logsContainer[0].scrollHeight);
    }
    
    /**
     * Complete processing
     */
    function completeProcessing(data) {
        updateProgress(data);
        
        if (data.logs && data.logs.length > 0) {
            addLogs(data.logs);
        }
        
        addLog('=== Processing completed! ===', 'success');
        
        // Show result message block
        const success = data.success || 0;
        const skipped = data.skipped || 0;
        const errors = data.errors || 0;
        
        $('#bpt-s-result-success').text(success);
        $('#bpt-s-result-skipped').text(skipped);
        $('#bpt-s-result-errors').text(errors);
        $('#bpt-s-result-message').show();
        
        // Scroll to result message
        $('html, body').animate({
            scrollTop: $('#bpt-s-result-message').offset().top - 50
        }, 500);
        
        // Re-enable form
        resetForm();
    }
    
    /**
     * Reset form
     */
    function resetForm() {
        $('#bpt-s-submit-btn').prop('disabled', false);
        $('.bpt-s-brand-checkbox, #bpt-s-product-type').prop('disabled', false);
        currentSessionId = null;
    }
    
    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
})(jQuery);

