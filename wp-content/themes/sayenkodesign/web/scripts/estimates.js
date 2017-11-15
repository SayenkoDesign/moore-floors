jQuery(function() {
    function Modifier(title, value, amount, amount2) {
        var parent = this;
        this.title = title;
        this.value = value;
        this.price = amount;
        this.price2 = amount2;
        this.found = true;

        this.createFromSelect = function(name) {
            var selector = 'select[name='+name+']';
            this.found = !!jQuery(selector).length;
            parent.value = jQuery(selector).val();
            parent.price = parseFloat(jQuery(selector + ' option:selected').attr('data-amount'));
            parent.price2 = parseFloat(jQuery(selector + ' option:selected').attr('data-amount2')) || 0;
            return parent;
        };

        this.createFromInput = function(selector) {
            this.found = !!jQuery(selector).length;
            parent.value = jQuery(selector).val();
            parent.price = parseFloat(jQuery(selector).attr('data-amount'));
            parent.price2 = parseFloat(jQuery(selector).attr('data-amount2')) || 0;
            return parent;
        };

        this.createFromRadio = function(name) {
            var selector = 'input[name='+name+']:checked';
            this.found = !!jQuery(selector).length;
            parent.value = jQuery(selector).val();
            parent.price = parseFloat(jQuery(selector).attr('data-amount'));
            parent.price2 = parseFloat(jQuery(selector).attr('data-amount26')) || 0;
            return parent;
        };
    }

    function RemovalRoom(el) {
        this.title = jQuery(el).attr('data-title');
        this.price = jQuery(el).attr('data-amount');
        this.square_feet = jQuery(el).val();

        this.getTotal = function(){
            return this.price * this.square_feet;
        }
    }

    var estimate = {
        current_step: 1,
        project_type: 0,
        details_section: '',
        base_price: 0,
        square_feet: 0,
        modifiers: [],
        removal: [],
        init: function(){
            // species click
            jQuery('.species input[type=radio]').on('change', function(){
                var id = jQuery(this).attr('id');
                jQuery('.species label.active').removeClass('active');
                jQuery('label[for=' + id + ']').addClass('active');
            });

            // show/hide removal section
            jQuery('select[name^="removal"]').on('change', function(){
                console.log("changed removal value");
                var val = jQuery(this).val();
                var yes = jQuery(this).parent('label').next('.yes');
                var no = jQuery(this).parent('label').next('.yes').next('.no');

                if(val == "yes") {
                    if(no.is(':visible')) {
                        no.slideToggle(function(){
                            yes.slideToggle();
                        })
                    } else {
                        yes.slideToggle();
                    }
                } else {
                    if(yes.is(':visible')) {
                        yes.slideToggle(function(){
                            no.slideToggle();
                        })
                    } else {
                        no.slideToggle();
                    }
                }
            });

            // change if own materials or not
            jQuery("select[name=ownB]").on('change', function() {
                var no = jQuery(this).parent('label').next('br').next('.no');
                no.slideToggle();
            });

            //show final form
            jQuery('a.ready').on("click", function(e){
                var submit = jQuery(this).siblings('.submit');
                jQuery(this).parent('div').next('form').slideToggle(function(){
                    submit.fadeToggle();
                });
                e.preventDefault();
                e.defaultPrevented = true;
                return false;
            });
        },
        addModifer: function(modifier) {
            if(modifier.found) {
                estimate.modifiers.push(modifier);
            }
        },
        setActiveStep: function(step){
            // set the right step number
            jQuery('.steps .active').removeClass('active');
            jQuery('.steps > div:nth-child(' + step + ') .step').addClass('active');
        },
        processStep1: function(){
            estimate.project_type = jQuery('input[name=project]:checked').val();
            estimate.details_section = jQuery('#project' + estimate.project_type);
            console.log('project type is ' + estimate.project_type);

            if(jQuery('#projectType').is(':visible')) {
                jQuery('#projectType').slideToggle(function () {
                    estimate.details_section.slideToggle()
                });
            } else {
                estimate.details_section.slideToggle();
            }
        },
        processStep2: function(){
            estimate.modifiers = [];
            estimate.removal = [];

            if(this.project_type != "D") {
                estimate.base_price = parseFloat(jQuery('input[name=Base' + this.project_type + ']').val());
                estimate.square_feet = jQuery('#square' + this.project_type).val();
            } else {
                estimate.base_price = [
                    parseFloat(jQuery('input[name=BaseA]').val()),
                    parseFloat(jQuery('input[name=BaseC]').val())
                ];
                estimate.square_feet = [
                    jQuery('#squareInstallation' + this.project_type).val(),
                    jQuery('#squareRefinish' + this.project_type).val()
                ];
            }

            if(this.project_type == "A") {
                estimate.addModifer(new Modifier('Wood Species').createFromRadio('species' + this.project_type));
                estimate.addModifer(new Modifier('Plank Width').createFromSelect('width' + this.project_type));
                estimate.addModifer(new Modifier("Stain").createFromRadio('stain' + this.project_type));
                estimate.addModifer(new Modifier("Finish").createFromRadio('finish' + this.project_type));
                estimate.addModifer(new Modifier("Baseboard removal and re-attach").createFromRadio('baseboard' + this.project_type));
            } else if(this.project_type == "B") {

                var own = new Modifier("Own Materials").createFromSelect('ownB');
                estimate.modifiers.push(own);
                if(own.value === "yes") {
                    console.log("user owns their own materials");
                    estimate.addModifer(new Modifier("Needs to be nailed to subfloor").createFromSelect('nailed' + this.project_type));
                } else {
                    console.log("user does not own their own materials");
                    estimate.addModifer(new Modifier("Wood Species").createFromRadio('species' + this.project_type));
                    estimate.addModifer(new Modifier("Wood Type").createFromSelect('solid' + this.project_type));
                    estimate.addModifer(new Modifier("Plank Width").createFromSelect('width' + this.project_type));
                    estimate.addModifer(new Modifier("Stain").createFromRadio('stain' + this.project_type));
                    estimate.addModifer(new Modifier("Finish").createFromRadio('finish' + this.project_type));
                    estimate.addModifer(new Modifier("Grade Level").createFromSelect('grade' + this.project_type));
                    estimate.addModifer(new Modifier("Nailed").createFromSelect('nailed' + this.project_type));
                }
                console.log(estimate);
            } else if(this.project_type == "C") {
                estimate.addModifer(new Modifier("Wood Species").createFromRadio('species' + this.project_type));
                estimate.addModifer(new Modifier("Stain").createFromRadio('stain' + this.project_type));
                estimate.addModifer(new Modifier("Finish").createFromRadio('finish' + this.project_type));
            } if(this.project_type == "D") {
                estimate.addModifer(new Modifier('Wood Species').createFromRadio('species' + this.project_type));
                estimate.addModifer(new Modifier('Plank Width').createFromSelect('width' + this.project_type));
                estimate.addModifer(new Modifier("Stain").createFromRadio('stain' + this.project_type));
                estimate.addModifer(new Modifier("Finish").createFromRadio('finish' + this.project_type));
                estimate.addModifer(new Modifier("Baseboard removal and re-attach").createFromRadio('baseboard' + this.project_type));
                return;
            }

            console.log("need to remove existing flooring?" + jQuery('select[name=removal' + this.project_type + '] option:selected').val());
            if(jQuery('select[name=removal' + this.project_type + '] option:selected').val() == "yes") {
                jQuery('input[name=removal' + this.project_type + 'Rooms]').each(function(){
                    if(jQuery(this).val() > 0) {
                        estimate.removal.push(new RemovalRoom(jQuery(this)));
                    }
                });
            }
        },
        processStep3: function() {
            jQuery('input[name=estimate]').val(estimate.getJson());
            jQuery('form.estimate-form-submit').submit();
        },
        getTotal: function() {
            if(estimate.project_type != "D") {
                var per_square_foot = this.base_price;
                for (var i = 0; i < this.modifiers.length; i++) {
                    per_square_foot += this.modifiers[i].price;
                }
                var installation_price = per_square_foot * this.square_feet;
                var removal_price = 0;
                if (this.removal.length) {
                    for (var j = 0; j < this.removal.length; j++) {
                        var cost = this.removal[j].getTotal();
                        removal_price += cost;
                        console.log(this.removal[j].square_feet + " sq/ft of " + this.removal[j].title + " will cost " + cost);
                    }
                }
                var total_price = installation_price + removal_price;
                console.log("price per square foot is " + per_square_foot);
                console.log("total price for installation is $" + installation_price);
                console.log("cost for removal is " + removal_price);
                console.log("total estimate is $" + total_price);
                return total_price;
            } else {
                var per_square_foot_installation = this.base_price[0];
                var per_square_foot_refinish = this.base_price[1];
                for (var i = 0; i < this.modifiers.length; i++) {
                    per_square_foot_installation += this.modifiers[i].price;
                    per_square_foot_refinish += this.modifiers[i].price2;
                }
                var installation_price = per_square_foot_installation * this.square_feet[0];
                var refinish_price = per_square_foot_refinish * this.square_feet[1];
                var removal_price = 0;
                if (this.removal.length) {
                    for (var j = 0; j < this.removal.length; j++) {
                        var cost = this.removal[j].getTotal();
                        removal_price += cost;
                        console.log(this.removal[j].square_feet + " sq/ft of " + this.removal[j].title + " will cost " + cost);
                    }
                }
                var total_price = installation_price + refinish_price + removal_price;
                console.log("price per square foot is " + (per_square_foot_installation + per_square_foot_refinish));
                console.log("total price for installation is $" + installation_price);
                console.log("total price for refinishing is $" + refinish_price);
                console.log("cost for removal is " + removal_price);
                console.log("total estimate is $" + total_price);
                console.log(estimate);
                return total_price;
            }
        },
        getService: function(){
            switch(this.project_type) {
                case 'A':
                    return 'Hardwood Floor Installation';
                    break;
                case 'B':
                    return 'Pre Finished or Engineered Hardwood Floor Installation';
                    break;
                case 'C':
                    return 'Refinish Existing Hardwood Flooring';
                    break;
                case 'D':
                    return 'Add on New Hardwood Flooring and Refinish Existing Hardwood Flooring';
                    break;
            }
        },
        getJson: function() {
            var data = {
                service: this.getService(),
                price: this.getTotal(),
                square_feet: this.square_feet,
                modifiers: [],
                removal: []
            };
            for(var i=0; i<this.modifiers.length;i++){
                data.modifiers.push({title: this.modifiers[i].title, value: this.modifiers[i].title});
            }
            if(this.removal.length) {
                for(var j=0; j<this.removal.length;j++){
                    data.removal.push({title: this.removal[j].title, value: this.removal[j].square_feet});
                }
            }
            return JSON.stringify(data);
        },
        showResults: function() {
            var title = "<h2><span class='primary-text'>you selected:</span> " + estimate.getService() + "</h2>";
            var price = "<tr><th>PRICE ESTIMATE</th><th>$" + estimate.getTotal() + "</th></tr>";
            var details = "";
            for(var i=0; i < estimate.modifiers.length; i++) {
                details += "<tr><td>" + estimate.modifiers[i].title + "</td><td>" + estimate.modifiers[i].value + "</td></tr>";
            }
            details += "<tr><td>Removal of Existing Floor Covering</td><td>";
            if(estimate.removal.length) {
                details += estimate.removal.map(function (el) {
                    return el.title
                }).join(', ');
            } else {
                details += "No";
            }
            details += "</td></tr>";
            details += "<tr><td>Sq Footage:</td><td>" + estimate.square_feet + "</td></tr>";
            var html = title + "<table><tbody>" + price + details + "</tbody></table>";
            jQuery('.estimate-form:visible').slideToggle(function() {
                jQuery('.estimate-details .quote').html(html);
                jQuery('.estimate-details').slideToggle();
            });
        }
    };

    estimate.init();

    // next button + submission
    jQuery('.estimate-form').on('submit', function(e){
        e.preventDefault();
        e.defaultPrevented = true;
        return false;
    });
    jQuery('.estimate-form').on('formvalid.zf.abide', function(e){
        console.log('hit next. processing step ' + estimate.current_step);
        if(estimate.current_step === 1) {
            estimate.processStep1();
            estimate.current_step = 2;
            estimate.setActiveStep(2);
        } else if(estimate.current_step === 2) {
            estimate.processStep2();
            estimate.showResults();
            estimate.current_step = 3;
            estimate.setActiveStep(3);
        } else if(estimate.current_step === 3) {
            estimate.processStep3();
        }
        e.preventDefault();
        e.defaultPrevented = true;
        return false;
    });

    // previous button
    jQuery('.estimate-form .button.prev').on('click', function(e){
        if(estimate.current_step === 3) {
            jQuery('.estimate-details:visible').slideToggle(function(){
                estimate.processStep1();
                estimate.current_step = 2;
                estimate.setActiveStep(2);
            });
        } else if(estimate.current_step === 2) {
            jQuery('.estimate-form:visible').slideToggle(function(){
                jQuery('#projectType').slideToggle();
                estimate.current_step = 1;
            })
        }
        e.preventDefault();
        e.defaultPrevented = true;
        return false;
    });

    // calculator assistance
    jQuery('.reveal .calculate').on('click', function(){
        var input = jQuery(jQuery(this).attr('data-field'));
        var lengthFt = parseInt(jQuery('input[name=calculateWidthFeet' + estimate.project_type + ']').val());
        var lengthIn = parseInt(jQuery('input[name=calculateWidthInches' + estimate.project_type + ']').val());
        var widthFt = parseInt(jQuery('input[name=calculateLengthFeet' + estimate.project_type + ']').val());
        var widthIn = parseInt(jQuery('input[name=calculateLengthInches' + estimate.project_type + ']').val());
        var length = lengthFt * 12 + lengthIn;
        var width = widthFt * 12 + widthIn;
        var square_feet = Math.floor(length * width / 144);
        var square_inches = (length * width /144) % 12;

        console.log("length " + length + " width " + width);
        console.log("area is " + square_feet + " feet and " + square_inches + " square inches");

        input.val(Math.round(length * width / 144));
        jQuery('#calculatorModal' + estimate.project_type).foundation('close');
    })
});
