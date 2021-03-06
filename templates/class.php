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
		{% for v in c.varname %}
			const {{v}} = {{c.value[loop.index0]}};
		{% endfor %}
	{% endfor %}

	{% for c in class_constants %}
		const {{c.group}} = [
			{% for val in c.varname %}
			self::{{val}} => {{c.label[loop.index0]}} {% if not loop.last %},{% endif %}
			{% endfor %}
		];
	{% endfor %}

	{% for prop in class_properties %}
		/**
		* {{prop.orm}}
		* @var {{prop.type}}
		*/
		{{prop.visibility}} {{prop.static}} ${{prop.name}} {% if prop.init is not empty %} = {{- prop.init -}} {%endif%};
	{% endfor %}

	/**
	* Auto generated constructor.
	*/
	public function __construct( {% for param in constructor_parameters %}{{param.classname}} {{param.name}}{% if not loop.last %},{% endif %} {% endfor %}){
		{% for param in constructor_parameters %}
			this.{{param.name}} = {{param.name}};
		{% endfor %}

		{% for b in constructorBody %}
			{{b}};
		{% endfor %}
	}

	{# ############### methods ################################# #}

	{% for m in methods %}
		/**
		* {{m.orm}}
		*/
		{{m.visibility}} {{m.static }} function {{m.functionName}}( {% for param in m.parameters %} /*{{param.type}}*/ ${{param.name}} {% endfor %}){
			{{m.body}}
		}
	{% endfor %}

	{# ############### getters and setters ################################# #}

	{% for prop in class_properties %}
		/**
		* @return {{prop.type}}
		*/
		{{prop.visibility}} {{prop.static}} function get{{prop.name|capitalize}}(){
			return $this->{{prop.name}};
		}

		/**
		* @param {{prop.type}} ${{prop.name}}
		* @return {{class_name}}
		*/
		{{prop.visibility}} {{prop.static}} function set{{prop.name|capitalize}}({{prop.type starts with '\\' ? prop.type : "" }} ${{prop.name}}){
			$this->{{prop.name}} = ${{prop.name}};
			return $this;
		}
	{% endfor %}
}