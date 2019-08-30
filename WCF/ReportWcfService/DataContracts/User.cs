using System;
using System.Runtime.Serialization;
using LinqToDB.Mapping;

namespace ReportWcfService
{
    [DataContract]
    [Table("user")]
    public class User
    {
        [DataMember]
        [Column("id", IsPrimaryKey = true)]
        public Int32 Id { get; set; }

        [DataMember]
        [Column("username")]
        public String Name { get; set; }

        [DataMember]
        [Column("password")]
        public String Password { get; set; }

        [DataMember]
        [Column("type")]
        public UserTypes Type { get; set; }

        [DataMember]
        [Column("active")]
        public Boolean IsActive { get; set; }

        [DataMember]
        [Column("institutionid")]
        public Int32 InstitutionId { get; set; }

        public User() { }
    }
}