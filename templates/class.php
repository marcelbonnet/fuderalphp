<?php
{% if license %}
/*
* {{license}}
*/
{% endif %}

{% if namespace %}
namespace {{namespace}};
{% endif %}

{% for param in implementing_interfaces|merge(extending_class)|sort %}
{% if param.classname is not empty %}
use {{param.fqcn}};
{% endif %}
{% endfor %}

{% if docs %}
/**
{% for key,val in docs %}
* @{{key}} {{val}}
{% endfor %}
* 
{% for param in class_orm %}
* {{param.annotation}}
{% endfor %}
*/
{% endif %}
{{class_visibility}} class {{class_name}} {% if extending_class and extending_class[0].classname is not empty %}extends {{extending_class[0].classname}} {% endif %}{% if implementing_interfaces is not empty %}implements {% for param in implementing_interfaces %}{{param.classname}}{% if not loop.last %},{% endif %} {% endfor %}{% endif %} {
	
	{% for c in class_constants %}
		{{c.tuple[0].val}}
		{% for t in c.tuple %}
			const {{t.varname}};
		{% endfor %}
	{% endfor %}

	{% for prop in class_properties %}
		{% set static = prop.is_static ? "static" : "" %}
		{% set const = prop.is_const ? "const" : "" %}
		/**
		* @var {{prop.type}}
		*/
		{{prop.visibility}} {{static}} {{const}} ${{prop.name}};
	{% endfor %}
	/**
	* Auto generated constructor.
	*/
	public function __construct( {% for param in constructor_parameters %}{{param.classname}} {{param.name}}{% if not loop.last %},{% endif %} {% endfor %}){
		{% for param in constructor_parameters %}
			this.{{param.name}} = {{param.name}};
		{% endfor %}
	}

	{# ############### getters and setters ################################# #}

	{% for prop in class_properties %}
		{% set static = prop.is_static ? "static" : "" %}
		{% set const = prop.is_const ? "const" : "" %}
		/**
		* @return {{prop.type}}
		*/
		{{prop.visibility}} {{static}} function get{{prop.name|capitalize}}(){
			return $this->{{prop.name}};
		}

		/**
		* @param {{prop.type}} ${{prop.name}}
		* @return {{class_name}}
		*/
		{{prop.visibility}} {{static}} function set{{prop.name|capitalize}}({{prop.type starts with '\\' ? prop.type : "" }} ${{prop.name}}){
			$this->{{prop.name}} = ${{prop.name}};
			return $this;
		}
	{% endfor %}
}